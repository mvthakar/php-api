<?php

require_once appPathOf('libs/AuthorizeBase.php');

class Authorize extends AuthorizeBase
{
    public static function forRoles(?array $roles = null, bool $allowExpiredJwt = false)
    {
        AuthorizeBase::_forRoles('error', $roles, $allowExpiredJwt);
    }
}