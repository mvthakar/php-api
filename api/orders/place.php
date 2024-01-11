<?php

Authorize::forRoles();
post();

$claims = Authorize::claims();
$db = Database::instance();

$userId = $claims["id"];

$inCartOrderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'In cart'");
$placedOrderStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'Placed'");

$cart = $db->get(
    "SELECT `id` FROM `orders` WHERE `orderStatusId` = ? AND `userId` = ?",
    [$inCartOrderStatus->id, $userId]
);

if ($cart == null)
    error(404);

$db->execute(
    "UPDATE `orders` SET `orderStatusId` = ? WHERE `id` = ?",
    [$placedOrderStatus->id, $cart->id]
);
