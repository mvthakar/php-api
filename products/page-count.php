<?php

get();

$db = Database::instance();
$result = $db->get("SELECT COUNT(*) AS `count` FROM `products` WHERE `isDeleted` = FALSE");

$itemsPerPage = $_GET['itemsPerPage'] ?? 10;
$pageCount = ceil($result->count / $itemsPerPage);

reply([$pageCount]);