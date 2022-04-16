<?php

require_once 'utilities.php';
require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

$course_number = "";
$term = "";
$rating = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    session_start();

    $sid = $userManager->Get_UserID($_SESSION["username"]);
    $tid = $userManager->Get_UserID($_POST["ta_username"]);
    $course_number = $_POST["course_number"];
    $term = $_POST["term"];
    $rating = intval($_POST["rating"]);
    $message = $_POST["message"];

    $rateErrCode = $courseManager->Rate_TA($sid, $tid, $course_number, $term, $rating, $message);

    if ($rateErrCode == 0)
    {
        header("Location: ../front-end/dashboard.php");
    }
    else
    {
        session_start();
        $_SESSION["errCode"] = $rateErrCode;
        header("Location: ../front-end/rate_ta.php");
    }

    die();
}