<?php

get();

$db = Database::instance();
$result = $db->get("SELECT COUNT(*) AS `count` FROM `categories`");

$itemsPerPage = 2;
$pageCount = ceil($result->count / $itemsPerPage);

reply([$pageCount]);