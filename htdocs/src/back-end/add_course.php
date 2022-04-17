<?php

require_once 'utilities.php';
require_once '../../matter/dbf/CourseManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

    $course_title = Utilities::cleanInput($_POST["title"]);
    $course_num = Utilities::cleanInput($_POST["number"]);
    $term = Utilities::cleanInput($_POST["term"]);

    $addErrCode = $courseManager->Add_Course($course_title, $course_num, $term);

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