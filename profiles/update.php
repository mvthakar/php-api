<?php

Authorize::forRoles(allowExpiredJwt: true);
$req = post();

$claims = Authorize::claims();
$db = Database::instance();

$userId = $claims["id"];

if (!isset($req->name) ||
    !isset($req->mobileNumber) ||
    !isset($req->address) ||
    !isset($req->pincode) ||
    !isset($req->city) ||
    !isset($req->state)
)
{
    error(400);
}

if (!Validator::mobileNumber($req->mobileNumber))
    error(400, ["Invalid mobile number"]);

if (!Validator::pincode($req->pincode))
    error(400, ["Invalid pincode"]);

$userProfile = $db->get(
    "SELECT * FROM `userProfiles` WHERE `userId` = ?",
    [$userId]
);

$query = "";
if ($userProfile == null)
{
    $query = "
        INSERT INTO 
            `userProfiles` (`name`, `mobileNumber`, `address`, `pincode`, `city`, `state`, `userId`)
        VALUES
            (?, ?, ?, ?, ?, ?, ?)
    ";
}
else
{
    $query = "
        UPDATE `userProfiles` SET 
            `name` = ?,
            `mobileNumber` = ?,
            `address` = ?,
            `pincode` = ?,
            `city` = ?,
            `state` = ?
        WHERE
            `userId` = ?
    ";
}

$params = [
    $req->name,
    $req->mobileNumber,
    $req->address,
    $req->pincode,
    $req->city,
    $req->state,
    $userId
];

$db->execute($query, $params);
