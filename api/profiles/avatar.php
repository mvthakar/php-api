<?php

Authorize::forRoles();
post();

$claims = Authorize::claims();

if (!isset($_FILES['avatar']))
    error(400);

$userId = $claims["id"];
$uploadedFile = $_FILES['avatar'];

$db = Database::instance();
$userProfile = $db->get(
    "SELECT `avatarFileName` FROM `userProfiles` WHERE `userId` = ?",
    [$userId]
);

if ($userProfile == null)
    error(404);

$avatarUploads = "public/uploads/avatars";
$fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
$fileName = time() . ".$fileExtension";
$filePath = pathOf("$avatarUploads/$fileName");

$allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$fileType = $finfo->file($uploadedFile['tmp_name']);

if(!in_array($fileType, $allowedImageTypes))
    error(400, ["Only jpg or png files are allowed"]);

move_uploaded_file($uploadedFile['tmp_name'], $filePath);

if ($userProfile->avatarFileName != null)
    unlink(pathOf("$avatarUploads/{$userProfile->avatarFileName}"));

$db->execute(
    "UPDATE `userProfiles` SET `avatarFileName` = ? WHERE `userId` = ?",
    [$fileName, $userId]
);
