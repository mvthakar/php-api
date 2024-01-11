<?php

function login(): array
{
    $req = post();
    if (!isset($req->email) || !isset($req->password))
        error(400);

    $email = $req->email;
    $password = $req->password;

    if (!Validator::email($email))
        error(400, ["Email is invalid"]);

    $db = Database::instance();
    $user = $db->get("SELECT `id`, `passwordHash`, `roleId` FROM `users` WHERE `email` = ?", [$email]);

    if ($user == null)
        error(401, ["Email or password is wrong"]);

    $passwordHashInDb = $user->passwordHash;
    if (!PasswordUtils::verify($password, $passwordHashInDb))
        error(401, ["Email or password is wrong"]);

    $roleId = $user->roleId;
    $role = $db->get("SELECT `name` FROM `roles` WHERE `id` = ?", [$roleId]);

    $tokenPayload = ["id" => $user->id, "email" => $email, "role" => $role->name];
    $accessToken = JwtUtils::generateAccessToken($tokenPayload);
    list($refreshToken, $issuedAt, $expiresAt) = JwtUtils::generateRefreshToken();
    
    $clientId = null;
    if (isset($_COOKIE['clientId']))
    {
        $userToken = $db->get(
            "SELECT `id` FROM `userTokens` WHERE `clientId` = ? AND `userId` = ?",
            [$_COOKIE['clientId'], $user->id]
        );

        if ($userToken != null)
        {
            $clientId = $_COOKIE['clientId'];
            $db->execute(
                "UPDATE `userTokens` SET `value` = ?, `issuedOnDateTime` = ?, `expiresOnDateTime` = ? WHERE `id` = ?",
                [$refreshToken, $issuedAt, $expiresAt, $userToken->id]
            );
        }
    }
    
    if ($clientId == null)
    {
        $clientId = GUID::generate();
        $tokenType = $db->get("SELECT `id` FROM `tokenTypes` WHERE `name` = 'RefreshToken'");

        $db->execute("
            INSERT INTO `userTokens` 
                (`tokenTypeId`, `clientId`, `value`, `issuedOnDateTime`, `expiresOnDateTime`, `userId`)
            VALUES
                (?, ?, ?, ?, ?, ?)
            ", [$tokenType->id, $clientId, $refreshToken, $issuedAt, $expiresAt, $user->id]
        );
    }

    return [
        "accessToken" => $accessToken, 
        "refreshToken" => $refreshToken, 
        "clientId" => $clientId
    ];
}
