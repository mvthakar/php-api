<?php

class TokenManager
{
    public static function generate($userId, $roleName): string
    {
        return "";
    }

    public static function decode($token): object
    {
        // decode token

        $object = new object();
        $object->userId = 1;
        $object->roleName = "User";

        return $object;
    }
}