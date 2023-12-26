<?php

class Authorize
{
    private static ?array $claims = null;
    public static function claims(): array { return self::$claims; }

    public static function forRoles(?array $roles = null, bool $allowExpiredJwt = false)
    {
        $authHeader = self::getAuthHeader();
        if ($authHeader == null)
            error(401);

        $token = self::getAccessTokenFromHeader($authHeader);

        $payload = JwtUtils::decode($token);
        if (count($payload) == 0)
            error(401);

        if ($roles != null && count($roles) > 0 && !in_array($payload["role"], $roles))
            error(401);

        if (!$allowExpiredJwt)
        {
            $expired = (new DateTime())->getTimestamp() > $payload["exp"];
            if ($expired)
                error(401);
        }

        self::$claims = $payload;
    }

    private static function getAuthHeader(): ?string
    {
        $headers = apache_request_headers();
        if(!isset($headers['Authorization']))
            return null;

        return $headers['Authorization'];
    }

    private static function getAccessTokenFromHeader(string $header): string
    {
        $search = "Bearer";
        $replace = "";

        if (!str_starts_with($header, "$search "))
            error(401);

        return implode($replace, explode($search, $header, 2));
    }
}