<?php

Authorize::forRoles(["Admin"]);
get();

if (!isset($_GET['id']))
    error(400);

$slug = $_GET['id'];

$db = Database::instance();

$category = $db->get("SELECT `id`, `name`, `imageFileName` FROM `categories` WHERE `slug` = ?", [$slug]);
if ($category == null)
    error(404);

$products = $db->get("SELECT COUNT(*) AS `count` FROM `productCategories` WHERE `categoryId` = ?", [$category->id]);
if ($products->count > 0)
    error(422, ["This category cannot be deleted because it has products."]);

$categoryUploads = "public/uploads/categories";
if ($category->imageFileName != null)
    unlink(pathOf("$categoryUploads/{$category->imageFileName}"));

$db->execute("DELETE FROM `categories` WHERE `slug` = ?", [$slug]);
