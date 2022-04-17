<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');

    $user_id = $userManager->Get_UserID($_POST["username"]);
    $property = $_POST["property"];
    $newValue = $_POST["newValue"];

    $editErrCode = $userManager->Update_User($user_id, $property, $newValue);

    if ($editErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $editErrCode;
        header("Location: ../front-end/?"); // TODO: fill in URL
    }

    die();
}