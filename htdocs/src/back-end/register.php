<?php

$username = "";
$password = "";
$userID = "";
$first_name = "";
$last_name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = Utilities::cleanInput($_POST["username"]);
    $password = Utilities::cleanInput($_POST["password"]);
    $userID = Utilities::cleanInput($_POST["user_id"]);
    $first_name = Utilities::cleanInput($_POST["first_name"]);
    $last_name = Utilities::cleanInput($_POST["last_name"]);
    $email = Utilities::cleanInput($_POST["email"]);

    $registerErrCode = UserManagement::Register_User($username, $password, $userID, $first_name, $last_name, $email);

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