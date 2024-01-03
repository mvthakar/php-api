<?php

Authorize::forRoles(allowExpiredJwt: true);
$req = post();

if (!isset($req->orderId))
    error(400);

$claims = Authorize::claims();
$userId = $claims["id"];

$db = Database::instance();
$cancelStatus = $db->get("SELECT `id` FROM `orderStatus` WHERE `name` = 'Canceled'");

$order = $db->get("SELECT `id`, (SELECT `name` FROM `orderStatus` WHERE `orderStatus`.`id` = `orders`.`orderStatusId`) AS `orderStatus` FROM `orders` WHERE `slug` = ?", [$req->orderId]);
if ($order == null)
    error(404);

if ($order->orderStatus != 'Placed' && $order->orderStatus != 'On the way')
    error(422, ["This order cannot be cancelled"]);

$db->execute(
    "UPDATE `orders` SET `orderStatusId` = ? WHERE `id` = ?",
    [$cancelStatus->id, $order->id]
);
