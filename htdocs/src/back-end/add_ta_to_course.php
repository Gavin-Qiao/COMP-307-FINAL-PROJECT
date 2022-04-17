<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    $user_id = $userManager->Get_UserID(Utilities::cleanInput($_POST["username"]));
    $role = Utilities::cleanInput($_POST["role"]);
    $course_num = Utilities::cleanInput($_POST["number"]);
    $term = Utilities::cleanInput($_POST["term"]);

    $addErrCode = $courseManager->Register_To_Course($user_id, $role, $course_num, $term);

    if ($addErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $addErrCode;
        header("Location: ../front-end/?"); // TODO: fill in URL
    }

    die();
}