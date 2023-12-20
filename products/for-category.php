<?php

get();

if (!isset($_GET['id']))
    error(400);

$categorySlug = $_GET['id'];

$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$offset = $itemsPerPage * ($pageNumber - 1);

if ($pageNumber < 1)
    error(400);

$db = Database::instance();
$items = $db->getAll("
SELECT 
`products`.`slug`,
`products`.`name`,
`products`.`price`,
(SELECT `productImages`.`imageFileName` FROM `productImages` WHERE `productImages`.`productId` = `products`.`id` LIMIT 1) AS `imageFileName`
FROM 
`productCategories`
INNER JOIN `products` ON `products`.`id` = `productCategories`.`productId`
WHERE 
`productCategories`.`categoryId` = (
    SELECT 
        `categories`.`id` 
    FROM 
        `categories` 
    WHERE 
        `categories`.`slug` = ?
)
AND
`products`.`isOutOfStock` = FALSE ORDER BY `products`.`updatedOnDateTime` DESC
LIMIT $itemsPerPage OFFSET $offset
", [$categorySlug]);

reply($items);