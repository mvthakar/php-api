<?php

Authorize::forRoles(["Admin"]);
$req = post();

if (!isset($_GET['id']) || !isset($req->name) || !isset($req->price) || !isset($req->description) || !isset($req->categories))
    error(400);

if (count($req->categories) == 0)
    error(400, ["Product must belong to at least one category"]);

$slug = $_GET['id'];

$db = Database::instance();
$product = $db->get(
    "SELECT `id` FROM `products` WHERE `slug` = ?",
    [$slug]
);

if ($product == null)
    error(404);

$name = $req->name;
$description = $req->description;
$price = $req->price;
$categories = $req->categories;

$db->execute(
    "UPDATE `products` SET `name` = ?, `description` = ?, `price` = ? WHERE `slug` = ?", 
    [$name, $description, $price, $slug]
);

$productId = $product->id;
$db->execute("DELETE FROM `productCategories` WHERE `productId` = ?", [$productId]);

$categorySlugsInQuotes = [];
foreach ($categories as $categorySlug)
    array_push($categorySlugsInQuotes, "'$categorySlug'");

$result = join(",", $categorySlugsInQuotes);
$categoryIds = $db->getAll("SELECT `id` FROM `categories` WHERE `slug` IN ($result)");

$setOfQuestionMarks = [];
$setOfQuestionMarkValues = [];
foreach ($categoryIds as $categoryId)
{
    array_push($setOfQuestionMarks, "(?, ?)");
    array_push($setOfQuestionMarkValues, $productId);
    array_push($setOfQuestionMarkValues, $categoryId->id);
}

$questionMarks = join(",", $setOfQuestionMarks);
$db->execute(
    "INSERT INTO `productCategories` (`productId`, `categoryId`) VALUES $questionMarks",
    $setOfQuestionMarkValues
);
