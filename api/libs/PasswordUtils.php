<?php

class PasswordUtils
{
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verify($password, $hash): bool
    {
        return password_verify($password, $hash);
    }
}