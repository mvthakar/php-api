<?php

$req = post();

if (!isset($req->email))
    error(400);

$email = $req->email;
if (!Validator::email($email))
        error(400, ["Email is invalid"]);

$db = Database::instance();
$result = $db->get("SELECT COUNT(*) AS `count` FROM `users` WHERE `email` = ?", [$email]);

if ($result->count > 0)
    error(409, ["This email cannot be used"]);

$otp = OtpManager::generateForUser($email);
// Mail::send($email, "OTP", $otp);
