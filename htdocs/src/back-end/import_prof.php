<?php

require_once '../../matter/dbf/CSV_Utilities.php';
require_once '../../matter/dbf/UserManagement.php';

session_start();

/*
 * File upload code adapted from: https://www.w3schools.com/php/php_file_upload.asp
 */

$target_file = basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if file already exists
if (file_exists($target_file))
{
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000)
{
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
if($fileType != "csv")
{
    echo "Sorry, only CSV files are allowed.";
    $uploadOk = 0;
}

// if everything is ok, try to upload file
if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) || $uploadOk == 0)
{
    echo "Sorry, there was an error uploading your file.";
}
else
{
    echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";

    $csv[] = CSV_Utilities::Read_CSV($target_file);

    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');

    foreach ($csv as $prof)
    {
        $registerErrCode = $userManager->Register_User($prof["username"], $prof["password"], $prof["user_id"], $prof["first_name"], $prof["last_name"], $prof["email"]);

        if ($registerErrCode == 0)
        {
            $roleErrCode = $userManager->Register_As("instructor", $prof["user_id"]);
        }
    }

    if (!unlink($target_file))
    {
        echo "There was a problem deleting the file.";
    }

    header("Location: ../front-end/?"); // TODO: fill in URL
    die();
}