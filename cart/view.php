<?php

Authorize::forRoles(allowExpiredJwt: true);
get();

$claims = Authorize::claims();
$db = Database::instance();

$userId = $claims["id"];
$orderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In cart'");
$order = $db->get("SELECT `id` FROM `orders` WHERE `orderStatusId` = ?", [$orderStatus->id]);

if ($order == null)
    error(404);

$existing = $db->getAll(
    "SELECT 
        *, 
        (SELECT 
                `price` 
            FROM 
                `products` 
            WHERE 
                `products`.`id` = `orderProducts`.`productId`
        ) AS `priceForSingleProduct`,
        (SELECT
                `imageFileName`
            FROM
                `productImages`
            WHERE
                `productId` = `orderProducts`.`productId`
            LIMIT 1
        ) AS `imageFileName`
        FROM 
            `orderProducts` 
        WHERE 
            `orderId` = ?",
[$order->id]);

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
        "price" => $priceWithTax,
        "imageFileName" => $orderProduct->imageFileName
    ]);
}

reply([
    "totalPriceWithTax" => $totalPriceWithTax,
    "totalPriceWithoutTax" => $totalPriceWithoutTax,
    "totalCgstAmount" => $totalCgstAmount,
    "totalSgstAmount" => $totalSgstAmount,
    "cgstPercentage" => $cgstPercentage,
    "sgstPercentage" => $sgstPercentage,
    "products" => $products
]);