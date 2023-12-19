<?php

get();

$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = 2;
$offset = $itemsPerPage * ($pageNumber - 1);

if ($pageNumber < 1)
    error(400);

$db = Database::instance();
$items = $db->getAll("SELECT `slug`, `name`, `imageFileName` FROM `categories` LIMIT $itemsPerPage OFFSET $offset");

reply($items);