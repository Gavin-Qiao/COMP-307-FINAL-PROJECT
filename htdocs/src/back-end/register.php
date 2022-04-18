<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');

    $username = Utilities::cleanInput($_POST["username"]);
    $password = Utilities::cleanInput($_POST["password"]);
    $userID = Utilities::cleanInput($_POST["user_id"]);
    $first_name = Utilities::cleanInput($_POST["first_name"]);
    $last_name = Utilities::cleanInput($_POST["last_name"]);
    $email = Utilities::cleanInput($_POST["email"]);

    $registerErrCode = $userManager->Register_User($username, $password, $userID, $first_name, $last_name, $email);

    session_start();
    if ($registerErrCode == 0)
    {
        $registerAsErrCode = $userManager->Register_As("student", $userID);
        if ($registerAsErrCode == 0)
        {
            header("Location: ../../index.php");
        }
        else
        {
            $_SESSION["errCode"] = $registerAsErrCode;
            header("Location: ../front-end/register.html");
        }
    }
    else
    {
        $_SESSION["errCode"] = $registerErrCode;
        header("Location: ../front-end/register.html");
    }

    die();
}