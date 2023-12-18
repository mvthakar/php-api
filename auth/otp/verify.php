<?php

$req = post();

if (!isset($req->email) || !isset($req->otp))
    error(400);

$email = $req->email;
$otp = $req->otp;

$valid = OtpManager::verifyForUser($email, $otp);
if (!$valid)
    error(403);
