<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';

$username = "";
$password = "";
$userID = "";
$first_name = "";
$last_name = "";
$email = "";

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

    if ($registerErrCode == 0)
    {
        header("Location: ../../index.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $registerErrCode;
        header("Location: ../front-end/register.php");
    }

    die();
}