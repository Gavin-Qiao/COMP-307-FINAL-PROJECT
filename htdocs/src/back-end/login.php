<?php

$username = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = Utilities::cleanInput($_POST["username"]);
    $password = Utilities::cleanInput($_POST["pass"]);

    $loginErrCode = UserManagement::Login_User($username, $password);
    session_start();

    if ($loginErrCode == 0)
    {
        $_SESSION["username"] = $username;
        $userid = UserManagement::Get_UserID($username);

        $_SESSION["student"] = "false";
        $_SESSION["ta"] = "false";
        $_SESSION["instructor"] = "false";
        $_SESSION["sysop"] = "false";
        $_SESSION["admin"] = "false";

        // TODO: get userIDs
        if (CourseManagement::Verify_Identity("student", $userid))
        {
            $_SESSION["student"] = "true";
        }
        if (CourseManagement::Verify_Identity("ta", $userid))
        {
            $_SESSION["ta"] = "true";
        }
        if (CourseManagement::Verify_Identity("instructor", $userid))
        {
            $_SESSION["instructor"] = "true";
        }
        if (CourseManagement::Verify_Identity("sysop", $userid))
        {
            $_SESSION["sysop"] = "true";
        }
        if (CourseManagement::Verify_Identity("admin", $userid))
        {
            $_SESSION["admin"] = "true";
        }

        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        $_SESSION["errCode"] = $loginErrCode;
        header("Location: ../index.html");
    }

    die();
}

