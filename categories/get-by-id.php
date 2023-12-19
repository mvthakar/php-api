<?php

get();

if (!isset($_GET['id']))
    error(400);

$slug = $_GET['id'];

$db = Database::instance();
$result = $db->get(
    "SELECT `name`, `imageFileName` FROM `categories` WHERE `slug` = ?",
    [$slug]
);

if ($result == null)
    error(404);

reply([$result]);