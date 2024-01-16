<?php

class AuthorizeBase
{
    private static ?array $claims = null;
    public static function claims(): array { return self::$claims; }

    protected static function _forRoles($errorCallback, ?array $roles = null, bool $allowExpiredJwt = false)
    {
        $authHeader = self::getAuthHeader();
        if ($authHeader == null)
            $errorCallback(401);

        $token = self::getAccessTokenFromHeader($authHeader);
        $payload = JwtUtils::decode($token);

        if (count($payload) == 0)
            $errorCallback(401);

        if ($roles != null && count($roles) > 0 && !in_array($payload["role"], $roles))
            $errorCallback(403);

        if (!$allowExpiredJwt)
        {
            $expired = (new DateTime())->getTimestamp() > $payload["exp"];
            if ($expired)
                $errorCallback(401);
        }

        self::$claims = $payload;
    }

    public static function onlyAnonymous()
    {
        $authHeader = self::getAuthHeader();
        if ($authHeader == null)
            return;

        $token = self::getAccessTokenFromHeader($authHeader);
        $payload = JwtUtils::decode($token);

        if (count($payload) == 0)
            return;

        $expired = (new DateTime())->getTimestamp() > $payload["exp"];
        if ($expired)
            return;

        error(403);
    }

    private static function getAuthHeader(): ?string
    {
        $headers = apache_request_headers();
        @$value = $headers['Authorization'] ?? $_COOKIE['accessToken'];

        return $value;
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