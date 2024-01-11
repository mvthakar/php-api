<?php

Authorize::forRoles(["Admin"]);
post();

if (!isset($_GET['id']) || !isset($_FILES['image']))
    error(400);

$slug = $_GET['id'];
$uploadedFile = $_FILES['image'];

$db = Database::instance();
$category = $db->get(
    "SELECT `imageFileName` FROM `categories` WHERE `slug` = ?",
    [$slug]
);

if ($category == null)
    error(404);

$categoryUploads = "public/uploads/categories";
$fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
$fileName = time() . ".$fileExtension";
$filePath = pathOf("$categoryUploads/$fileName");

$allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$fileType = $finfo->file($uploadedFile['tmp_name']);

if(!in_array($fileType, $allowedImageTypes))
    error(400, ["Only jpg or png files are allowed"]);

move_uploaded_file($uploadedFile['tmp_name'], $filePath);

if ($category->imageFileName != null)
    unlink(pathOf("$categoryUploads/{$category->imageFileName}"));

$db->execute(
    "UPDATE `categories` SET `imageFileName` = ? WHERE `slug` = ?",
    [$fileName, $slug]
);
