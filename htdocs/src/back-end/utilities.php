<?php

require_once '../../matter/dbf/UserManagement.php';
require_once '../../matter/dbf/CourseManagement.php';

class Utilities
{
    /**
     * Removes slashes and converts special characters into HTML entities
     * @param string $input
     * @return string
     */
    static function cleanInput(string $input) : string
    {
        $input = trim($input);
        $input = stripslashes($input);
        return htmlspecialchars($input);
    }

    /**
     * Returns a list of users that TA a particular student
     * @param string $sid Student ID
     * @return array
     */
    static function getTAByStudent(string $sid) : array
    {
        $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');
        $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

        $courses[] = $courseManager->Get_Courses_By_Role($sid, "student");

        $tas = array();

        $allUsers[] = $userManager->Get_All_Users();

        foreach ($courses as $course)
        {
            foreach ($allUsers as $user)
            {
                if ($courseManager->Verify_In_Course($user["USER_ID"], "ta", $course["COURSE_NUM"], $course["TERM_YEAR"]) == 0)
                {
                    $tas[] = $user;
                }
            }
        }

        return $tas;
    }

    /**
     * Returns a list of courses that a user is registered in as a specific role
     * @param string $userid User ID
     * @param string $role Either "ta", "student", or "instructor"
     * @return array
     */
    static function getCoursesByRole(string $userid, string $role) : array
    {
        $courseManager = new CourseManagement('../../matter/dbf/TA_Management_Website.db');

        return $courseManager->Get_Courses_By_Role($userid, $role);
    }
}