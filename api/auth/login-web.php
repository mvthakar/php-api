<?php

require_once './login-base.php';

$tokens = login();

setcookie(
    "accessToken", 
    "Bearer {$tokens["accessToken"]}", 
    expires_or_options: time() + 2592000, 
    path: "/", 
    domain: "localhost"
);

setcookie(
    "refreshToken", 
    $tokens["refreshToken"], 
    expires_or_options: time() + 2592000,
    path: "/", 
    domain: "localhost"
);

setcookie(
    "clientId", 
    $tokens["clientId"], 
    expires_or_options: time() + 2592000,
    path: "/", 
    domain: "localhost"
);
