<?php

Authorize::forRoles();
get();

$claims = Authorize::claims();
$userId = $claims["id"];

$db = Database::instance();
$profile = $db->get(
    "SELECT `name`, `mobileNumber`, `address`, `pincode`, `city`, `state`, `avatarFileName` FROM `userProfiles` WHERE `userId` = ?",
    [$userId]
);

if ($profile == null)
    error(404, ["Profile not found for this user"]);

reply([$profile]);
