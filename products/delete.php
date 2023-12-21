<?php

Authorize::forRoles(["Admin"]);
get();

if (!isset($_GET['id']))
    error(400);

$slug = $_GET['id'];

$db = Database::instance();
$product = $db->get(
    "SELECT `id` FROM `products` WHERE `slug` = ?",
    [$slug]
);

if ($product == null)
    error(404);

$productId = $product->id;
$productImagesPath = pathOf("public/uploads/products");

$productImages = $db->getAll(
    "SELECT `imageFileName` FROM `productImages` WHERE `productId` = ?",
    [$productId]
);

foreach ($productImages as $productImage)
{
    $fileName = $productImage->imageFileName;
    unlink("$productImagesPath/$fileName");
}

$db->execute("DELETE FROM `productImages` WHERE `productId` = ?", [$productId]);
$db->execute("DELETE FROM `productCategories` WHERE `productId` = ?", [$productId]);
$db->execute("DELETE FROM `products` WHERE `id` = ?", [$productId]);
