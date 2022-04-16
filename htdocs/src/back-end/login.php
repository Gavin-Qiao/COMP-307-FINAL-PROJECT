<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

$username = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    $username = Utilities::cleanInput($_POST["username"]);
    $password = Utilities::cleanInput($_POST["pass"]);

    $loginErrCode = $userManager->Login_User($username, $password);
    session_start();

    if ($loginErrCode == 0)
    {
        $_SESSION["username"] = $username;
        $userid = $userManager->Get_UserID($username);

        $_SESSION["student"] = "false";
        $_SESSION["ta"] = "false";
        $_SESSION["instructor"] = "false";
        $_SESSION["sysop"] = "false";
        $_SESSION["admin"] = "false";

        if ($courseManager->Verify_Identity("student", $userid))
        {
            $_SESSION["student"] = "true";
        }
        if ($courseManager->Verify_Identity("ta", $userid))
        {
            $_SESSION["ta"] = "true";
        }
        if ($courseManager->Verify_Identity("instructor", $userid))
        {
            $_SESSION["instructor"] = "true";
        }
        if ($courseManager->Verify_Identity("sysop", $userid))
        {
            $_SESSION["sysop"] = "true";
        }
        if ($courseManager->Verify_Identity("admin", $userid))
        {
            $_SESSION["admin"] = "true";
        }

        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        $_SESSION["errCode"] = $loginErrCode;
        header("Location: ../../index.html");
    }

    die();
}

