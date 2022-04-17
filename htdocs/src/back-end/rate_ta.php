<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    session_start();

    $sid = $userManager->Get_UserID($_SESSION["username"]);
    $tid = $userManager->Get_UserID(Utilities::cleanInput($_POST["ta_username"]));
    $course_number = Utilities::cleanInput($_POST["course_number"]);
    $term = Utilities::cleanInput($_POST["term"]);
    $rating = intval(Utilities::cleanInput($_POST["rating"]));
    $message = Utilities::cleanInput($_POST["message"]);

    $rateErrCode = $courseManager->Rate_TA($sid, $tid, $course_number, $term, $rating, $message);

    if ($rateErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        $_SESSION["errCode"] = $rateErrCode;
        header("Location: ../front-end/?"); // TODO: fill in URL
    }

    die();
}