<?php

Authorize::forRoles(allowExpiredJwt: true);
$req = post();

$claims = Authorize::claims();
$userId = $claims["id"];

$clientId = $req->clientId ?? $_COOKIE['clientId'] ?? null;
$db = Database::instance();

if ($clientId != null)
{
    $db->execute(
        "DELETE FROM `userTokens` WHERE `userId` = ? AND `clientId` = ?",
        [$userId, $clientId]
    );
}
else
{
    $db->execute(
        "DELETE FROM `userTokens` WHERE `userId` = ?",
        [$userId]
    );
}
