<?php

class Authorize
{
    public static function forRoles(?array $roles = null)
    {
        $authHeader = self::getAuthHeader();
        $token = self::getTokenFromHeader($authHeader);

        $payload = JwtUtils::decode($token);
        if (count($payload) == 0)
            error(401);

        if ($roles == null || count($roles) == 0)
            return;
        
        if (!in_array($payload["role"], $roles))
            error(401);
    }

    private static function getAuthHeader(): ?string
    {
        $headers = apache_request_headers();
        if(!isset($headers['Authorization']))
            return null;

        return $headers['Authorization'];
    }

    private static function getTokenFromHeader(string $header): string
    {
        $search = "Bearer";
        $replace = "";

        return implode($replace, explode($search, $header, 2));
    }
}