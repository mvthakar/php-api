<?php

get();

if (!isset($_GET['id']))
    error(400);

$slug = $_GET['id'];

$db = Database::instance();
$product = $db->get(
    "SELECT `id`, `name`, `description`, `price` FROM `products` WHERE `slug` = ? AND `isDeleted` = FALSE",
    [$slug]
);

if ($product == null)
    error(404);

$productCategories = $db->getAll(
    "
    SELECT 
        `categories`.`slug`, `categories`.`name` 
    FROM 
        `productCategories` 
    INNER JOIN 
        `categories` ON `categories`.`id` = `productCategories`.`categoryId` 
    WHERE 
        `productCategories`.`productId` = ?
    ",
    [$product->id]
);

$productImages = $db->getAll(
    "SELECT `id`, `imageFileName` FROM `productImages` WHERE `productId` = ?",
    [$product->id]
);

reply([[
    "product" => [
        "name" => $product->name,
        "description" => $product->description,
        "price" => $product->price,
        "categories" => $productCategories,
        "images" => $productImages
    ]
]]);