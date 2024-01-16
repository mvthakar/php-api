<?php

Authorize::forRoles(allowExpiredJwt: true);
$claims = Authorize::claims();

if (!isset($_COOKIE["clientId"]) || !isset($_COOKIE["refreshToken"]))
    error(400);

$tokens = TokenUtils::generateNewTokens($claims, $_COOKIE["clientId"], $_COOKIE["refreshToken"]);
if ($tokens == null)
    error(401);

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
