<?php

Authorize::forRoles();
$req = post();

if (!isset($req->products))
    error(400);

$claims = Authorize::claims();
$db = Database::instance();

$orderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In cart'");
$order = $db->get(
    "SELECT `id` FROM `orders` WHERE `userId` = ? AND `orderStatusId` = ?", 
    [$claims['id'], $orderStatus->id]
);

$updated = $req->products;
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
    [$order->id]
);

for ($j = count($updated) - 1; $j >= 0; $j--)
{
    for ($i = count($existing) - 1; $i >= 0; $i--)
    {
        if ($existing[$i]->slug == $updated[$j]->id)
        {
            $existing[$i]->quantity = $updated[$j]->quantity;
            unset($updated[$j]);

            break;
        }
    }
}

$case = "(CASE `slug`";
$setOfQuestionMarks = [];
$setOfSlugsForParams = [];
$setOfSlugsForCase = [];

foreach ($existing as $item)
{
    array_push($setOfQuestionMarks, "?");
    array_push($setOfSlugsForParams, $item->slug);
 
    array_push($setOfSlugsForCase, $item->slug);
    array_push($setOfSlugsForCase, $item->quantity);

    $case .= " WHEN ? THEN ?";
}
$case .= " END)";

$questionMarks = join(", ", $setOfQuestionMarks);
$query = "UPDATE `orderProducts` SET `quantity` = $case WHERE `slug` in ($questionMarks)";
$params = array_merge($setOfSlugsForCase, $setOfSlugsForParams);

$db->execute($query, $params);

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