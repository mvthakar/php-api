<?php

class JwtUtils
{
    private const KEY = "This is obviously a strong key!";
    private const ALGO = "AES-256-CBC";
    private const VEC = "dAG1l?ZaLrVljhXz";

    public static function create(array $payload): string
    {
        $payloadJson = json_encode($payload);
        $token = openssl_encrypt($payloadJson, self::ALGO, self::KEY, 0, self::VEC);

        return $token;
    }

    public static function decode(string $token): array
    {
        $payloadJson = openssl_decrypt($token, self::ALGO, self::KEY, 0, self::VEC);
        $payload = json_decode($payloadJson);

        return (array)$payload;
    }
}