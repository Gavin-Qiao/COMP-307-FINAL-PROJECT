<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    session_start();

    $course_id = $_POST["course_id"];
    $instructor_id = $userManager->Get_UserID($_SESSION["username"]);
    $ta_id = $userManager->Get_UserID($_POST["ta_username"]);
    $message = $_SESSION["message"];

    $logErrCode = $courseManager->Instructor_Log($course_id, $instructor_id, $ta_id, $message);

    if ($logErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $logErrCode;
        header("Location: ../front-end/?"); // TODO: fill in URL
    }

    die();
}