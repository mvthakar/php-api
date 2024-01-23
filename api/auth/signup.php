<?php

function verifyEmail($email)
{
    if (!Validator::email($email))
        error(400, ["Email is invalid"]);

    $db = Database::instance();
    $result = $db->get("SELECT COUNT(*) AS `count` FROM `users` WHERE `email` = ?", [$email]);

    if ($result->count > 0)
        error(409, ["This email cannot be used"]);
}

function verifyPassword($password)
{
    $passwordErrors = Validator::password($password);
    
    if (count($passwordErrors) > 0)
        error(400, $passwordErrors);
}

function checkForVerifiedOtp($email)
{
    if (!OtpManager::isVerified($email))
        error(403, ["Could not sign up. Please try again."]);
}

function createUser($email, $password)
{
    $db = Database::instance();

    $authProvider = $db->get("SELECT `id` FROM `authProviders` WHERE `name` = 'Email'");
    $role = $db->get("SELECT `id` FROM `roles` WHERE `name` = 'User'");

    $passwordHash = PasswordUtils::hash($password);
    $userSlug = GUID::generate();
    $authProviderId = $authProvider->id;
    $roleId = $role->id;

    $db->execute(
        "INSERT INTO `users` (`slug`, `email`, `passwordHash`, `authProviderId`, `roleId`) VALUES (?, ?, ?, ?, ?)",
        [$userSlug, $email, $passwordHash, $authProviderId, $roleId]
);
}

$req = post();
if (!isset($req->email) || !isset($req->password))
    error(400);

$email = $req->email;
$password = $req->password;

verifyEmail($email);
verifyPassword($password);

checkForVerifiedOtp($email);
createUser($email, $password);

OtpManager::deleteOtpForUser($email);
reply(["Signed up successfully. An OTP has been sent to your email"]);
