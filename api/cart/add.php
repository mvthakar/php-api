<?php

Authorize::forRoles();
$req = post();

if (!isset($req->products))
    error(400);

$claims = Authorize::claims();
$db = Database::instance();

$slug = GUID::generate();
$orderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In Cart'");
$userId = $claims["id"];

$userProfile = $db->get("SELECT COUNT(*) AS `count` FROM `userProfiles` WHERE `userId` = ?", [$userId]);
if ($userProfile->count == 0)
    error(422, ["Please complete your profile first"]);

$orderProducts = $req->products;

$setOfSlugs = [];
$setOfQuestionMarks = [];
foreach ($orderProducts as $orderProduct)
{
    array_push($setOfSlugs, $orderProduct->id);
    array_push($setOfQuestionMarks, "?");
}

$questionMarks = join(",", $setOfQuestionMarks);
$products = $db->getAll(
    "SELECT `id`, `price`, `isOutOfStock` FROM `products` WHERE `slug` IN ($questionMarks)", 
    $setOfSlugs
);

if (count($orderProducts) != count($products))
    error(422);

$outOfStockErrors = [];
for ($i = 0; $i < count($products); $i++)
{
    if ($products[$i]->isOutOfStock)
        array_push($outOfStockErrors, ["id" => $products[$i]->slug]);

    $orderProducts[$i]->productId = $products[$i]->id;
    $orderProducts[$i]->price = $products[$i]->price;
}

if (count($outOfStockErrors) > 0)
    error(422, $outOfStockErrors);

$existingCart = $db->get("
    SELECT 
        `id`,
        `totalPriceWithoutTax`, 
        `cgstAmount`, 
        `sgstAmount`, 
        `totalPriceWithTax`
    FROM
        `orders`
    WHERE
        `orderStatusId` = ? AND `userId` = ?
", [$orderStatus->id, $userId]);

$existingCartProducts = [];
if ($existingCart != null)
{
    $existingCartProducts = $db->getAll(
        "SELECT `productId`, `quantity` FROM `orderProducts` WHERE `orderId` = ?",
        [$existingCart?->id]
    );
}

$result = GstUtils::calculate($orderProducts);

$cgstPercentage = 9;
$sgstPercentage = 9;
$totalPriceWithoutTax = ($existingCart?->totalPriceWithoutTax ?? 0) + $result["totalPriceWithoutTax"];
$totalCgstAmount = ($existingCart?->cgstAmount ?? 0) + $result["totalCgstAmount"];
$totalSgstAmount = ($existingCart?->sgstAmount ?? 0) + $result["totalSgstAmount"];
$totalPriceWithTax = ($existingCart?->totalPriceWithTax ?? 0) + $result["totalPriceWithTax"];

$orderId = -1;
if ($existingCart != null)
{
    $orderId = $existingCart->id;

    $db->execute("
        UPDATE `orders` SET
            `totalPriceWithoutTax` = ?, 
            `cgstPercentage` = ?, 
            `cgstAmount` = ?, 
            `sgstPercentage` = ?, 
            `sgstAmount` = ?, 
            `totalPriceWithTax` = ?
        WHERE
            `id` = ?
    ", 
    [ 
        $totalPriceWithoutTax,
        $cgstPercentage,
        $totalCgstAmount,
        $sgstPercentage,
        $totalSgstAmount,
        $totalPriceWithTax,
        $orderId
    ]);
}
else
{
    $db->execute("
        INSERT INTO `orders` 
        (
            `slug`, 
            `totalPriceWithoutTax`, 
            `cgstPercentage`, 
            `cgstAmount`, 
            `sgstPercentage`, 
            `sgstAmount`, 
            `totalPriceWithTax`, 
            `orderStatusId`, 
            `userId`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $slug, 
            $totalPriceWithoutTax, 
            $cgstPercentage, 
            $totalCgstAmount, 
            $sgstPercentage, 
            $totalSgstAmount,
            $totalPriceWithTax,
            $orderStatus->id,
            $userId
        ]
    );

    $orderId = $db->lastInsertId();
}

foreach ($orderProducts as $orderProduct)
{
    $orderProduct->updateQuantity = $orderProduct->quantity;

    foreach ($existingCartProducts as $existingProduct)
    {
        if ($orderProduct->productId == $existingProduct->productId)
        {
            $orderProduct->updateQuantity += $existingProduct->quantity;
            break;
        }
    }

    $query = "
        INSERT INTO 
            `orderProducts` (`slug`, `quantity`, `orderId`, `productId`) 
        VALUES 
            (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE `quantity` = ?";

    $params = [
        GUID::generate(), 
        $orderProduct->quantity, 
        $orderId, 
        $orderProduct->productId, 
        $orderProduct->updateQuantity
    ];

    $db->execute($query, $params);
}
