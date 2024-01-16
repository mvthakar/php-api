<?php

class JwtUtilsBase
{
    private const KEY = "This is obviously a strong key!";
    private const ALGO = "AES-256-CBC";
    private const VEC = "dAG1l?ZaLrVljhXz";

    public static function generateAccessToken(array $payload): string
    {
        $currentTime = new DateTime();
        $expiresOnTime = $currentTime->add(DateInterval::createFromDateString('10 seconds'));

        $currentTimestamp = $currentTime->getTimestamp();
        $expiry = $expiresOnTime->getTimestamp();

        $payload["iat"] = $currentTimestamp;
        $payload["exp"] = $expiry;

        $payloadJson = json_encode($payload);
        $token = openssl_encrypt($payloadJson, self::ALGO, self::KEY, 0, self::VEC);

        return $token;
    }

    public static function generateRefreshToken(): array
    {
        $currentTime = new DateTime();
        $expiresOnTime = (new DateTime())->add(DateInterval::createFromDateString('30 days'));

        $currentTimeFormatted = $currentTime->format('Y-m-d H:i:s');
        $expiresOnTimeFormatted = $expiresOnTime->format('Y-m-d H:i:s');

        $payload["iat"] = $currentTime->getTimestamp();
        $payload["exp"] = $expiresOnTime->getTimestamp();

        $payloadJson = json_encode($payload);
        $token = openssl_encrypt($payloadJson, self::ALGO, self::KEY, 0, self::VEC);

        return [$token, $currentTimeFormatted, $expiresOnTimeFormatted];
    }

    public static function decode(string $token): array
    {
        $payloadJson = openssl_decrypt($token, self::ALGO, self::KEY, 0, self::VEC);
        $payload = json_decode($payloadJson);

        return (array)$payload;
    }
}