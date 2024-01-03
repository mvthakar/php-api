<?php

Authorize::forRoles(allowExpiredJwt: true);
get();

$claims = Authorize::claims();

$status = $_GET['status'] ?? null;
$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$offset = $itemsPerPage * ($pageNumber - 1);

$userId = $claims["id"];
$db = Database::instance();

$selectedStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = ?", [$status]);
$inCartOrderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In cart'");

$part = $selectedStatus == null ? "" : "AND `orderStatusId` = {$selectedStatus->id}";

$ordersExceptInCart = $db->getAll(
    "SELECT 
        `slug`, 
        `orderedOnDateTime`, 
        `deliveredOnDateTime`, 
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
        `userId` = ? 
        AND 
        `orderStatusId` != ?
        $part 
    ORDER BY `orderedOnDateTime` DESC
    LIMIT $itemsPerPage OFFSET $offset",
    [$userId, $inCartOrderStatus->id]
);

reply($ordersExceptInCart);