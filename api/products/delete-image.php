<?php

Authorize::forRoles(["Admin"]);
get();

if (!isset($_GET['id']))
    error(400);

$id = $_GET['id'];

$db = Database::instance();
$image = $db->get("SELECT `imageFileName` FROM `productImages` WHERE `id` = ?", [$id]);

if ($image == null)
    error(404);

unlink(pathOf("public/uploads/products/{$image->imageFileName}"));
$db->execute("DELETE FROM `productImages` WHERE `id` = ?", [$id]);
