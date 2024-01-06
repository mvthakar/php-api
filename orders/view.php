<?php

Authorize::forRoles();
$req = post();

if (!isset($req->orderId))
    error(400);

$claims = Authorize::claims();
$userId = $claims["id"];

$db = Database::instance();

$orderSlug = $req->orderId;
$order = $db->get(
    "SELECT 
        `id`,
        `orderedOnDateTime`,
        `deliveredOnDateTime`,
        `totalPriceWithoutTax`,
        `cgstPercentage`,
        `cgstAmount`,
        `sgstPercentage`,
        `sgstAmount`,
        `totalPriceWithTax`, 
        (SELECT 
            `name` 
        FROM 
            `orderStatus` 
        WHERE 
            `orderStatus`.`id` = `orders`.`orderStatusId`
        ) AS `orderStatus` 
    FROM 
        `orders` 
    WHERE 
    `slug` = ? AND `userId` = ?",
    [$orderSlug, $userId]
);

if ($order == null)
    error(404);

$orderProducts = $db->getAll("
    SELECT 
        `products`.`name`,
        `products`.`price` AS `individualPrice`,
        `orderProducts`.`quantity`,
        (`products`.`price` * `orderProducts`.`quantity`) AS `price`,
        (
            SELECT 
                `productImages`.`imageFileName` 
            FROM 
                `productImages` 
            WHERE 
                `products`.`id` = `productImages`.`productId`
            LIMIT 1
        ) AS `productImage`
    FROM 
        `orderProducts`
    INNER JOIN 
        `products`
    ON
        `products`.`id` = `orderProducts`.`productId`
    WHERE `orderId` = ?
    ",
    [$order->id]
);

unset($order->id);
$order->products = $orderProducts;

reply([$order]);