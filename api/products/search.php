<?php

get();

if (!isset($_GET['search']))
    error(400);

$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$includeOutOfStock = $_GET['includeOutOfStock'] ?? null;
$search = $_GET['search'];

$offset = $itemsPerPage * ($pageNumber - 1);

if ($pageNumber < 1)
    error(400);

$searchPart = "";
$includeOutOfStockPart = "";
$params = [];

if (isset($search))
{
    $searchPart = "AND `products`.`name` LIKE ?";
    array_push($params, "%$search%");
}

if (!isset($includeOutOfStock))
    $includeOutOfStockPart = "AND `products`.`isOutOfStock` = FALSE";
    
$db = Database::instance();

$count = $db->get("
    SELECT 
        COUNT(`name`) AS `totalItems`
        FROM 
        `products`
    WHERE 
        `products`.`isDeleted` = FALSE
    $includeOutOfStockPart
    $searchPart
", $params);

$items = $db->getAll("
    SELECT 
        `products`.`slug`,
        `products`.`name`,
        `products`.`price`,
        (
            SELECT 
                `productImages`.`imageFileName` 
            FROM 
                `productImages` 
            WHERE 
                `productImages`.`productId` = `products`.`id` 
            LIMIT 1
        ) AS `imageFileName`
    FROM 
        `products`
    WHERE 
        `products`.`isDeleted` = FALSE
    $includeOutOfStockPart
    $searchPart
    ORDER BY 
        `products`.`updatedOnDateTime` 
    DESC 
    LIMIT $itemsPerPage OFFSET $offset
", $params);

reply([["pageCount" => ceil($count->totalItems / $itemsPerPage), "products" => $items]]);
