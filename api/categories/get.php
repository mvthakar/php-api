<?php

get();

$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$search = $_GET['search'] ?? null;

$offset = $itemsPerPage * ($pageNumber - 1);

if ($pageNumber < 1)
    error(400);

$part = "";
$params = null;

if (isset($search))
{
    $part = "WHERE `name` LIKE ?";
    $params = ["%$search%"];
}

$db = Database::instance();
$count = $db->get(
    "SELECT COUNT(*) AS `totalItems` FROM `categories` $part",
    $params
);

$items = $db->getAll(
    "SELECT `slug`, `name`, `imageFileName` FROM `categories` $part LIMIT $itemsPerPage OFFSET $offset",
    $params
);

reply([["pageCount" => ceil($count->totalItems / $itemsPerPage), "categories" => $items]]);