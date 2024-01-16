<?php

require_once appPathOf('libs/AuthorizeBase.php');

class Authorize extends AuthorizeBase
{
    public static function forRoles(?array $roles = null, bool $allowExpiredJwt = false)
    {
        AuthorizeBase::_forRoles('Authorize::error', $roles, $allowExpiredJwt);
    }

    public static function error($statusCode)
    {
        if ($statusCode == 403)
        {
            echo "<script>window.location.href = '" . urlOf('index.php') . "';</script>";    
            return;
        }

        if (!isset($_COOKIE['accessToken']))
        {
            echo "<script>window.location.href = '" . urlOf('login.php') . "';</script>";
            return;
        }

        echo "<script>" .
        "async function bro()" .
        "{" .
        "    let refreshResult = await request('auth/tokens/refresh-web.php', 'POST', null, true, false);" .
        "    if (refreshResult.status != 200)" .
        "        window.location.href = 'login.php';" .
        "}" .
        "$(bro);" .
        "</script>";
    }
}