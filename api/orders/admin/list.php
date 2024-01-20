<?php

// Authorize::forRoles(["Admin"]);
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
        `userProfiles`.`name`, 
        `userProfiles`.`mobileNumber`,
        `orders`.`slug`, 
        `orders`.`orderedOnDateTime`, 
        `orders`.`deliveredOnDateTime`, 
        `orders`.`totalPriceWithTax`,
        (
            SELECT `orderStatus`.`name` FROM `orderStatus` WHERE `orderStatus`.`id` = `orders`.`orderStatusId` 
        ) AS `orderStatus`
    FROM 
        `orders` 
    LEFT JOIN
        `userProfiles`
    ON
        `userProfiles`.`userId` = `orders`.`userId`
    WHERE `orders`.`orderStatusId` != ?
        $selectedStatusWhere 
    ORDER BY `orders`.`orderedOnDateTime` DESC
    LIMIT $itemsPerPage OFFSET $offset",
    [$inCartOrderStatus->id]
);

reply($orders);