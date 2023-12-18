<?php

$req = post();
if (!isset($req->email) || !isset($req->password))
    error(400);

$email = $req->email;
$password = $req->password;

if (!Validator::email($email))
    error(400, ["Email is invalid"]);

$db = Database::instance();
$result = $db->get("SELECT `passwordHash`, `roleId` FROM `users` WHERE `email` = ?", [$email]);

if ($result == null)
    error(401, ["Email or password is wrong"]);

$passwordHashInDb = $result->passwordHash;
if (!PasswordUtils::verify($password, $passwordHashInDb))
    error(401, ["Email or password is wrong"]);

$roleId = $result->roleId;
$role = $db->get("SELECT `name` FROM `roles` WHERE `id` = ?", [$roleId]);

$tokenPayload = ["email" => $email, "role" => $role->name];
$token = JwtUtils::create($tokenPayload);

token($token);
