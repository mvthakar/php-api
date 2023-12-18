<?php

class Validator
{
    public static function email($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) != '';
    }

    public static function password($password): array
    {
        $passwordPatterns = [
            ["/[A-Z]+/", "Password must have at least one capital letter."],
            ["/[a-z]+/", "Password must have at least one small letter."],
            ["/[0-9]+/", "Password must have at least one digit."],
            ["/[^A-Za-z0-9]+/", "Password must have at least one special character."],
            ["/^.{8,}$/", "Password must be at least 8 characters."]
        ];
        
        $passwordErrors = [];
        foreach ($passwordPatterns as $pattern)
        {
            if (!preg_match($pattern[0], $password))
                array_push($passwordErrors, $pattern[1]);
        }

        return $passwordErrors;
    }
}