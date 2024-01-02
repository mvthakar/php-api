<?php

Authorize::forRoles(allowExpiredJwt: true);

$req = post();

$claims = Authorize::claims();
$db = Database::instance();

$userId = $claims["id"];

if (!isset($req->oldPassword) || !isset($req->newPassword))
    error(400);

$oldPassword = $req->oldPassword;
$newPassword = $req->newPassword;

$user = $db->get("SELECT `passwordHash` FROM `users` WHERE `id` = ?", [$userId]);
if ($user == null)
    error(404);

$valid = PasswordUtils::verify($oldPassword, $user->passwordHash);
if (!$valid)
    error(403, ["Old password is wrong"]);

$passwordErrors = Validator::password($newPassword);
if (count($passwordErrors) > 0)
    error(400, $passwordErrors);

$newPasswordHash = PasswordUtils::hash($newPassword);
$db->execute(
    "UPDATE `users` SET `passwordHash` = ? WHERE `id` = ?",
    [$newPasswordHash, $userId]
);
