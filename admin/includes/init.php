<?php

define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('APP_DIR', 'project');
define('ADMIN_DIR', "project/admin");

date_default_timezone_set("Asia/Calcutta");

function appPathOf(string $path): string
{
    return ROOT . "/" . APP_DIR . "/" . $path;
}

function pathOf(string $path): string
{
    return ROOT . "/" . ADMIN_DIR . "/" . $path;
}

function urlOf(string $path)
{
    return "/" . ADMIN_DIR . "/" . $path;
}

function active(string $linkName)
{
    $currentUrl = $_SERVER['REQUEST_URI'];
    return (str_contains($currentUrl, $linkName)) ? "active" : "";
}

require_once appPathOf("libs/Database.php");
require_once appPathOf("libs/TokenUtils.php");

require_once pathOf('libs/JwtUtils.php');
require_once pathOf('libs/Authorize.php');

if (str_ends_with($_SERVER['REQUEST_URI'], 'login.php')) 
{
    require_once pathOf('includes/header-login.php');
} 
else 
{
    require_once pathOf('includes/header.php');
    Authorize::forRoles(["Admin"]);
}
