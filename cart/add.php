<?php

Authorize::forRoles(allowExpiredJwt: true);
$req = post();

if (!isset($req->products))
    error(400);

$claims = Authorize::claims();
$db = Database::instance();

$slug = GUID::generate();
$orderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In Cart'");
$userId = $claims["id"];

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

    $orderProducts[$i]->product = $products[$i];
}

if (count($outOfStockErrors) > 0)
    error(422, $outOfStockErrors);

$totalPriceWithTax = 0;
$totalPriceWithoutTax = 0;
$totalCgstAmount = 0;
$totalSgstAmount = 0;

$cgstPercentage = 9;
$sgstPercentage = 9;
$totalGstPercentage = $cgstPercentage + $sgstPercentage;

foreach($orderProducts as $orderProduct)
{
    $priceWithTax = round($orderProduct->quantity * $orderProduct->product->price, 2);
    $gstAmount = round($priceWithTax - $priceWithTax / (1.0 + ($totalGstPercentage / 100)), 2);
    $cgstAmount = round($gstAmount * $cgstPercentage / $totalGstPercentage, 2);
    $sgstAmount = round($gstAmount * $sgstPercentage / $totalGstPercentage, 2);
    $priceWithoutTax = round($priceWithTax - $cgstAmount - $sgstAmount, 2);

    $totalPriceWithTax += $priceWithTax;
    $totalCgstAmount += $cgstAmount;
    $totalSgstAmount += $sgstAmount;
    $totalPriceWithoutTax += $priceWithoutTax;
}

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

$setOfQuestionMarks = [];
$setOfOrderProducts = [];

foreach ($orderProducts as $orderProduct)
{
    array_push($setOfQuestionMarks, "(?, ?, ?, ?)");
    array_push($setOfOrderProducts, GUID::generate());
    array_push($setOfOrderProducts, $orderProduct->quantity);
    array_push($setOfOrderProducts, $orderId);
    array_push($setOfOrderProducts, $orderProduct->product->id);
}

$questionMarks = join(",", $setOfQuestionMarks);
$db->execute(
    "INSERT INTO `orderProducts` (`slug`, `quantity`, `orderId`, `productId`) VALUES $questionMarks",
    $setOfOrderProducts
);
