<?php

Authorize::forRoles(["Admin"]);
$req = post();

if (!isset($req->orderId) || !isset($req->status))
    error(400);

$orderSlug = $req->orderId;
$statusName = $req->status;

$db = Database::instance();

$order = $db->get("SELECT `id` FROM `orders` WHERE `slug` = ?", [$orderSlug]);
$status = $db->get("SELECT `id`, `name` FROM `orderStatus` WHERE `name` = ?", [$statusName]);

if ($order == null || $status == null)
    error(404);

if ($status->name != 'On the way' && 
    $status->name != 'Delivered' && 
    $status->name != 'Rejected')
    error(422, ["Cannot change status for this order"]);

$deliveredOnDateTime = $status->name == 'Delivered' ? 'CURRENT_TIMESTAMP()' : 'null';
$db->execute("
        UPDATE 
            `orders` 
        SET 
            `orderStatusId` = ? , 
            `deliveredOnDateTime` = $deliveredOnDateTime 
        WHERE `id` = ?
    ",
    [$status->id, $order->id]
);

// TODO: Send FCM notification to the user
// TODO: Send email to user