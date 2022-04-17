<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    $user_id = $userManager->Get_UserID(Utilities::cleanInput($_POST["username"]));
    $course_id = Utilities::cleanInput($_POST["course_id"]);

    $removeErrCode = $courseManager->Remove_From_Course($user_id, "ta", $course_id);

    if ($removeErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $removeErrCode;
        header("Location: ../front-end/?"); // TODO: fill in URL
    }

    die();
}