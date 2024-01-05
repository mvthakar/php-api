<?php

define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('APP_DIR', "/api");

header('Content-Type: application/json');
date_default_timezone_set("Asia/Calcutta");

function pathOf(string $path): string
{
    return ROOT . "/" . APP_DIR . "/" . $path;
}

function urlOf(string $path)
{
    return "/" . APP_DIR . "/" . $path;
}

function tokens(array $tokens)
{
    echo json_encode($tokens);
}

function reply(array $response)
{
    echo json_encode(["messages" => $response]);
}

function error(int $statusCode, ?array $response = null)
{
    http_response_code($statusCode);
    die($response != null ? json_encode(["messages" => $response]) : "");
}

function get()
{
    if ($_SERVER['REQUEST_METHOD'] != 'GET')
    {
        http_response_code(405);
        die();
    }
}

function post()
{
    if ($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        http_response_code(405);
        die();
    }

    $rawRequest = file_get_contents("php://input");
    return json_decode($rawRequest);
}

require_once pathOf("/libs/Database.php");
require_once pathOf("/libs/OtpManager.php");
require_once pathOf("/libs/Mail.php");
require_once pathOf("/libs/GUID.php");
require_once pathOf("/libs/PasswordUtils.php");
require_once pathOf("/libs/GstUtils.php");
require_once pathOf("/libs/Validator.php");
require_once pathOf("/libs/JwtUtils.php");
require_once pathOf("/libs/Authorize.php");
