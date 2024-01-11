<?php

require_once './refresh-base.php';

Authorize::forRoles(allowExpiredJwt: true);
$req = post();

if (!isset($req->clientId) || !isset($req->refreshToken))
    error(400);

$tokens = generateNewTokens($req->clientId, $req->refreshToken);
tokens($tokens);
