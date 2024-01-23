<?php

Authorize::forRoles(allowExpiredJwt: true);
$claims = Authorize::claims();

$req = post();

if (!isset($req->clientId) || !isset($req->refreshToken))
    error(400);

$tokens = TokenUtils::generateNewTokens($claims, $req->clientId, $req->refreshToken);
if ($tokens == null)
    error(401);

tokens($tokens);
