<?php

Authorize::forRoles(["Admin"]);
post();

if (!isset($_GET['id']))
    error(400);

$slug = $_GET['id'];
$uploadedFiles = $_FILES['images'];

$db = Database::instance();
$product = $db->get(
    "SELECT `id` FROM `products` WHERE `slug` = ?",
    [$slug]
);

if ($product == null)
    error(404);

$db = Database::instance();
$productId = $product->id;

$productUploads = "public/uploads/products";
$allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];

$setOfQuestionMarks = [];
$setOfQuestionMarkValues = [];

$error = 400;
$errorMessages = [];

for ($i = 0; $i < count($uploadedFiles['name']); $i++)
{
    $fileExtension = pathinfo($uploadedFiles['name'][$i], PATHINFO_EXTENSION);
    $fileName = "$i-" . time() . ".$fileExtension";
    $filePath = pathOf("$productUploads/$fileName");

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $fileType = $finfo->file($uploadedFiles['tmp_name'][$i]);

    if(!in_array($fileType, $allowedImageTypes))
    {
        array_push($errorMessages, "Error uploading '{$uploadedFiles['name'][$i]}'. Only jpg or png files are allowed");
        continue;
    }

    move_uploaded_file($uploadedFiles['tmp_name'][$i], $filePath);

    array_push($setOfQuestionMarks, "(?, ?)");
    array_push($setOfQuestionMarkValues, $fileName);
    array_push($setOfQuestionMarkValues, $productId);
}

$questionMarks = join(",", $setOfQuestionMarks);
$db->execute(
    "INSERT INTO `productImages` (`imageFileName`, `productId`) VALUES $questionMarks",
    $setOfQuestionMarkValues
);

if (count($errorMessages) > 0)
    error($error, $errorMessages);
