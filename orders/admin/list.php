<?php

Authorize::forRoles(["Admin"], allowExpiredJwt: true);
get();

$status = $_GET['status'] ?? null;
$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$offset = $itemsPerPage * ($pageNumber - 1);

$db = Database::instance();

$inCartOrderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In cart'");
$selectedStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = ?", [$status]);
$selectedStatusWhere = $selectedStatus == null ? "" : " AND `orderStatusId` = {$selectedStatus->id}";

$orders = $db->getAll(
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
        WHERE `orderStatusId` != ?
        $selectedStatusWhere 
    ORDER BY `orderedOnDateTime` DESC
    LIMIT $itemsPerPage OFFSET $offset",
    [$inCartOrderStatus->id]
);

reply($orders);