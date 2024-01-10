<?php

function generateNewTokens($clientId, $refreshToken)
{
    if ($clientId == null || $refreshToken == null)
        error(400);
    
    $claims = Authorize::claims();
    $userId = $claims["id"];
    $email = $claims["email"];
    $role = $claims["role"];
    
    $accessToken = JwtUtils::generateAccessToken([
        "id" => $userId, 
        "email" => $email, 
        "role" => $role
    ]);
    
    list($refreshToken, $issuedAt, $expiresAt) = JwtUtils::generateRefreshToken();
    
    $db = Database::instance();
    $db->execute(
        "UPDATE `userTokens` SET `value` = ?, `issuedOnDateTime` = ?, `expiresOnDateTime` = ? WHERE `userId` = ? AND `clientId` = ?",
        [$refreshToken, $issuedAt, $expiresAt, $userId, $clientId]
    );
    
    return [
        "accessToken" => $accessToken, 
        "refreshToken" => $refreshToken, 
        "clientId" => $clientId
    ];
}