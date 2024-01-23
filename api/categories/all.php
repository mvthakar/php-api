<?php

get();

$db = Database::instance();
$items = $db->getAll("SELECT `slug`, `name` FROM `categories`");

reply([["categories" => $items]]);
