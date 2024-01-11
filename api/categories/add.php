<?php

Authorize::forRoles(["Admin"]);
$req = post();

if (!isset($req->name))
    error(400);

$slug = GUID::generate();
$name = $req->name;

$db = Database::instance();
$db->execute(
    "INSERT INTO `categories` (`slug`, `name`) VALUES (?, ?)", 
    [$slug, $name]
);

reply([$slug]);
