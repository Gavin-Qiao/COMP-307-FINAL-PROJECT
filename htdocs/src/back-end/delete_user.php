<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');

    $user_id = $userManager->Get_UserID($_POST["username"]);

    $deleteErrCode = $userManager->Delete_User($user_id);

    if ($deleteErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $deleteErrCode;
        header("Location: ../front-end/?"); // TODO: fill in URL
    }

    die();
}