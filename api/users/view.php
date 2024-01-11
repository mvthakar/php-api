<?php

Authorize::forRoles(["Admin"]);
get();

$pageNumber = $_GET['page'] ?? 1;
$itemsPerPage = $_GET['count'] ?? 10;
$offset = $itemsPerPage * ($pageNumber - 1);

if ($pageNumber < 1)
    error(400);

$db = Database::instance();
$users = $db->getAll("
    SELECT 
        `userProfiles`.`name`, 
        `userProfiles`.`mobileNumber`, 
        `users`.`email` 
    FROM 
        `users` 
    LEFT JOIN 
        `userProfiles` 
    ON 
        `users`.`id` = `userProfiles`.`userId` 
    LIMIT $itemsPerPage OFFSET $offset
");

reply($users);