<?php

require_once './refresh-base.php';
Authorize::forRoles(allowExpiredJwt: true);

if (!isset($_COOKIE["clientId"]) || !isset($_COOKIE["refreshToken"]))
    error(400);

$tokens = generateNewTokens($_COOKIE["clientId"], $_COOKIE["refreshToken"]);

setcookie("refreshToken", $tokens["refreshToken"], path: "/", domain: "localhost");
setcookie("clientId", $tokens["clientId"], path: "/", domain: "localhost");

tokens(["accessToken" => $tokens["accessToken"]]);
