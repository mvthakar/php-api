<?php

class TokenUtils
{
    public static function generateNewTokens($claims, $clientId, $refreshToken)
    {
        $userId = $claims["id"];
        $email = $claims["email"];
        $role = $claims["role"];
        
        $db = Database::instance();
        $tokenType = $db->get("SELECT `id` FROM `tokenTypes` WHERE `name` = 'RefreshToken'");
        
        $refreshTokenInDb = $db->get(
            "SELECT `expiresOnDateTime` FROM `userTokens` WHERE `tokenTypeId` = ? AND `clientId` = ? AND `userId` = ? AND `value` = ?",
            [$tokenType->id, $clientId, $userId, $refreshToken]
        );

        if ($refreshTokenInDb == null)
            return null;

        $expired = (new DateTime())->getTimestamp() > (new DateTime($refreshTokenInDb->expiresOnDateTime))->getTimestamp();
        if ($expired)
            return null;

        $accessToken = JwtUtils::generateAccessToken([
            "id" => $userId, 
            "email" => $email, 
            "role" => $role
        ]);
        
        list($refreshToken, $issuedAt, $expiresAt) = JwtUtils::generateRefreshToken();
        
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
}