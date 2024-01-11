<?php

Authorize::forRoles(["Admin"]);
$req = post();

if (!isset($_GET['id']) || !isset($req->name))
    error(400);

$slug = $_GET['id'];
$name = $req->name;

$db = Database::instance();

$category = $db->get("SELECT `name` FROM `categories` WHERE `slug` = ?", [$slug]);
if ($category == null)
    error(404);

$db->execute(
    "UPDATE `categories` SET `name` = ? WHERE `slug` = ?", 
    [$name, $slug]
);
