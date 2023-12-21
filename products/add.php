<?php

Authorize::forRoles(["Admin"]);
$req = post();

if (!isset($req->name) || !isset($req->price) || !isset($req->description) || !isset($req->categories))
    error(400);

if (count($req->categories) == 0)
    error(400, ["Product must belong to at least one category"]);

$slug = GUID::generate();
$name = $req->name;
$description = $req->description;
$price = $req->price;
$categories = $req->categories;

$db = Database::instance();
$db->execute(
    "INSERT INTO `products` (`slug`, `name`, `description`, `price`) VALUES (?, ?, ?, ?)", 
    [$slug, $name, $description, $price]
);

$productId = $db->lastInsertId();

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

reply([$slug]);
