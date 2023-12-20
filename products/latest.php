<?php

get();

$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$offset = $itemsPerPage * ($pageNumber - 1);

if ($pageNumber < 1)
    error(400);

$db = Database::instance();
$items = $db->getAll("
    SELECT `slug`, `name`, `price`, (SELECT `imageFileName` FROM `productImages` WHERE `productId` = `products`.`id` LIMIT 1) AS `imageFileName`
    FROM `products` WHERE `isOutOfStock` = FALSE ORDER BY `updatedOnDateTime` DESC LIMIT $itemsPerPage OFFSET $offset
");

reply($items);