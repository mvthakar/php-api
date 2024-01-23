<?php

Authorize::forRoles(["Admin"]);
get();

if (!isset($_GET['id']))
    error(400);

$db = Database::instance();

$orderSlug = $_GET['id'];
$order = $db->get("
    SELECT 
        `orders`.`id`,
        `orders`.`orderedOnDateTime`,
        `orders`.`deliveredOnDateTime`,
        `orders`.`totalPriceWithoutTax`,
        `orders`.`cgstPercentage`,
        `orders`.`cgstAmount`,
        `orders`.`sgstPercentage`,
        `orders`.`sgstAmount`,
        `orders`.`totalPriceWithTax`, 
        (
            SELECT 
                `name` 
            FROM 
                `orderStatus` 
            WHERE 
                `orderStatus`.`id` = `orders`.`orderStatusId`
        ) AS `orderStatus`,
        (
            SELECT
                `email`
            FROM
                `users`
            WHERE
                `users`.`id` = `orders`.`userId`
        ) AS `userEmail`,
        `userProfiles`.`name`,
        `userProfiles`.`mobileNumber`,
        `userProfiles`.`address`,
        `userProfiles`.`pincode`,
        `userProfiles`.`city`,
        `userProfiles`.`state`
    FROM 
        `orders`
    INNER JOIN 
        `userProfiles`
    ON
        `orders`.`userId` = `userProfiles`.`userId`
    WHERE 
        `slug` = ?
    ", [$orderSlug]
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
    WHERE 
        `orderId` = ?
    ", [$order->id]
);

unset($order->id);
$order->products = $orderProducts;

reply([$order]);