<?php

require_once './login-base.php';

$tokens = login();

setcookie("refreshToken", $tokens["refreshToken"], path: "/", domain: "localhost");
setcookie("clientId", $tokens["clientId"], path: "/", domain: "localhost");

tokens(["accessToken" => $tokens["accessToken"]]);