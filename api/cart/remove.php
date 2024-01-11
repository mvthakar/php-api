<?php

Authorize::forRoles();
$req = post();

$claims = Authorize::claims();
$db = Database::instance();

$orderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In cart'");
$order = $db->get("SELECT `id` FROM `orders` WHERE `orderStatusId` = ?", [$orderStatus->id]);

if ($order == null)
    error(404);

$userId = $claims["id"];

if (!isset($req->products))
{
    $db->execute(
        "DELETE FROM `orders` WHERE `userId` = ? AND `orderStatusId` = ?",
        [$userId, $orderStatus->id]
    );

    exit();
}

$orderProducts = $req->products;

$setOfSlugs = [];
$setOfQuestionMarks = [];
foreach ($orderProducts as $orderProduct)
{
    array_push($setOfSlugs, $orderProduct->id);
    array_push($setOfQuestionMarks, "?");
}
$questionMarks = join(",", $setOfQuestionMarks);

$db->execute(
    "DELETE FROM `orderProducts` WHERE `slug` IN ($questionMarks)",
    $setOfSlugs
);

$existing = $db->getAll(
    "SELECT 
        *, 
        (SELECT 
                `price` 
            FROM 
                `products` 
            WHERE 
                `products`.`id` = `orderProducts`.`productId`
        ) 
        AS `priceForSingleProduct` 
        FROM 
            `orderProducts` 
        WHERE 
            `orderId` = ?",
[$order->id]);

if (count($existing) == 0)
{
    $db->execute(
        "DELETE FROM `orders` WHERE `id` = ?",
        [$order->id]
    );

    exit();
}

$totalPriceWithTax = 0;
$totalPriceWithoutTax = 0;
$totalCgstAmount = 0;
$totalSgstAmount = 0;

$cgstPercentage = 9;
$sgstPercentage = 9;

$totalGstPercentage = $cgstPercentage + $sgstPercentage;
$products = [];

foreach($existing as $orderProduct)
{
    $priceWithTax = round($orderProduct->quantity * $orderProduct->priceForSingleProduct, 2);
    $gstAmount = round($priceWithTax - $priceWithTax / (1.0 + ($totalGstPercentage / 100)), 2);
    $cgstAmount = round($gstAmount * $cgstPercentage / $totalGstPercentage, 2);
    $sgstAmount = round($gstAmount * $sgstPercentage / $totalGstPercentage, 2);
    $priceWithoutTax = round($priceWithTax - $cgstAmount - $sgstAmount, 2);

    $totalPriceWithTax += $priceWithTax;
    $totalCgstAmount += $cgstAmount;
    $totalSgstAmount += $sgstAmount;
    $totalPriceWithoutTax += $priceWithoutTax;

    array_push($products, [
        "id" => $orderProduct->slug, 
        "quantity" => $orderProduct->quantity,
        "price" => $priceWithTax
    ]);
}

$db->execute(
    "UPDATE `orders` SET 
        `totalPriceWithoutTax` = ?,
        `cgstPercentage` = ?,
        `cgstAmount` = ?,
        `sgstPercentage` = ?,
        `sgstAmount` = ?,
        `totalPriceWithTax` = ?
    WHERE `id` = ?",
    [
        $totalPriceWithoutTax, 
        $cgstPercentage, 
        $totalCgstAmount, 
        $sgstPercentage, 
        $totalSgstAmount,
        $totalPriceWithTax,
        $order->id
    ]
);

reply([
    "totalPriceWithTax" => $totalPriceWithTax,
    "totalPriceWithoutTax" => $totalPriceWithoutTax,
    "totalCgstAmount" => $totalCgstAmount,
    "totalSgstAmount" => $totalSgstAmount,
    "cgstPercentage" => $cgstPercentage,
    "sgstPercentage" => $sgstPercentage,
    "products" => $products
]);
