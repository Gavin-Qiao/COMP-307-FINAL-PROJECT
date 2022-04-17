<?php

require_once 'DatabaseDriver.php';
require_once 'SQL_STATEMENTS.php';

class CourseManagement extends DatabaseDriver
{


    /**
     * Decode CID to TERM_YEAR and COURSE_NUM
     * @param String $CourseID e.g., WINTER2022_COMP307
     * @return array e.g., [[TERM_YEAR] => WINTER2022, [COURSE_NUM] => COMP307]
     */
    function Decode_CourseID(String $CourseID): array
    {
        if (!function_exists('str_contains'))
        {
            function str_contains($haystack, $needle): bool
            {
                return $needle !== '' && mb_strpos($haystack, $needle) !== false;
            }
        }

        if (empty($CourseID) || !str_contains($CourseID, "_"))
            return array();

        $expose_course = explode("_", $CourseID);
        return
        [
            "TERM_YEAR"   => $expose_course[0],
            "COURSE_NUM"  => $expose_course[1]
        ];
    }

    /**
     * Encode CID from TERM_YEAR and COURSE_NUM
     * @param String $term e.g., WINTER2022
     * @param String $num  e.g., COMP307
     * @return String e.g., WINTER2022_COMP307
     */
    function Encode_CourseID(String $term, String $num): String
    {
        return $term.'_'.$num;
    }



    /** TODO: ADD 'SINCE' (probably)
     * Query the database to get all the courses related to the given user.
     * @param  String $userID
     * @return array
     * [                                           <br>
     * &nbsp;[COURSE_TITLE] => Title of the course <br>
     * &nbsp;[COURSE_NUM]   => e.g., COMP308       <br>
     * &nbsp;[TERM_YEAR]    => e.g., WINTER2022    <br>
     * ]
     * <p> []: no record found or some exceptions </p>
     */
    function Get_Courses(String $userID): array
    {
        // Verify userID
        if (strlen($userID) != 9 || preg_match('#[^0-9]#', $userID))
            return []; // Incorrect userID format: should be 9 digits

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::FIND_COURSE);

            // Execute
            $statement->execute([$userID, $userID, $userID]);

            $courses = [];
            $c_query = $conn->prepare($statement_library::GET_COURSE_TITLE);
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
            {
                $cid = $row['CID'];
                $c_query->execute([$row['CID']]);
                $cid_decode = $this->Decode_CourseID($cid);
                $courses[] =
                [
                    'COURSE_TITLE' => $c_query->fetch(\PDO::FETCH_ASSOC)['COURSE_TITLE'],
                    'COURSE_NUM'   => $cid_decode['COURSE_NUM'],
                    'TERM_YEAR'    => $cid_decode['TERM_YEAR']
                ];
            }
            return $courses;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return [];
        }
    }

    /** TODO: ADD 'SINCE' (probably)
     * Query the database to get all the courses related to the given user in given role.
     * @param String $userID
     * @param String $role
     * @return array
     * <p> array['CNUM', 'CTERM']                 </p>
     * <p> []: no record found or some exceptions </p>
     */
    function Get_Courses_By_Role(String $userID, String $role) : array
    {
        // Verify identity
        $errCode = $this->Verify_Identity($role, $userID);
        if ($errCode != 0)
            return [];

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = null;

            switch ($role)
            {
                case 'student':
                {
                    $statement = $conn->prepare($statement_library::FIND_STUDENT_COURSE);
                    break;
                }
                case 'ta':
                {
                    $statement = $conn->prepare($statement_library::FIND_TA_COURSE);
                    $statement->execute([$userID]);
                    $courses = [];
                    $c_query = $conn->prepare($statement_library::GET_COURSE_TITLE);
                    while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
                    {
                        $cid = $row['CID'];
                        $c_query->execute([$row['CID']]);
                        $cid_decode = $this->Decode_CourseID($cid);
                        $courses[] =
                            [
                                'COURSE_TITLE' => $c_query->fetch(\PDO::FETCH_ASSOC)['COURSE_TITLE'],
                                'COURSE_NUM'   => $cid_decode['COURSE_NUM'],
                                'TERM_YEAR'    => $cid_decode['TERM_YEAR' ],
                                'SINCE'        => $row       ['SINCE'     ],
                                'HOURS'        => $row       ['HOURS'     ],
                                'NOTE'         => $row       ['NOTE']
                            ];
                    }
                    return $courses;
                }
                case 'instructor':
                {
                    $statement = $conn->prepare($statement_library::FIND_INSTRUCTOR_COURSE);
                    break;
                }
                default:
                {
                    echo "Unknown identity: ".$role.PHP_EOL;
                    return [];
                }
            }

            // Execute
            $statement->execute([$userID]);

            $courses = [];
            $c_query = $conn->prepare($statement_library::GET_COURSE_TITLE);
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
            {
                $cid = $row['CID'];
                $c_query->execute([$row['CID']]);
                $cid_decode = $this->Decode_CourseID($cid);
                $courses[] =
                [
                    'COURSE_TITLE' => $c_query->fetch(\PDO::FETCH_ASSOC)['COURSE_TITLE'],
                    'COURSE_NUM'   => $cid_decode['COURSE_NUM'],
                    'TERM_YEAR'    => $cid_decode['TERM_YEAR' ],
                    'SINCE'        => $row       ['SINCE']
                ];
            }
            return $courses;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return [];
        }
    }

    /**
     * Add a course to the database
     * @param  String $title title of the course (e.g., Introduction to web development)
     * @param  String $num   course number (4 uppercase letters + 3 digits, e.g., COMP307)
     * @param  String $term  course term (WINTER/FALL/SUMMER + 4 digits, e.g., WINTER2022)
     * @return int
     * <p>errCode: Result of adding course                                                 </p>
     * <ul>
     * <li> 0:  Success                                                                    </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-13: Invalid course_title length: ]0, 64] expected!                             </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * </ul>
     */
    function Add_Course(String $title, String $num, String $term): int
    {
        // Verify course_title
        if (empty($title) || strlen($title) > 64)
            return -13; // Invalid course_title length: ]0, 64] expected!

        // Verify course_num
        $num = strtoupper($num);
        if (empty($num) || !preg_match("/^[A-Z]{4}[0-9]{3}$/", $num))
            return -14; // Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!

        // Verify course_term
        $term = strtoupper($term);
        if(empty($term) || !preg_match("/^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/", $term))
            return -15; // Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected!

        try
        {
            // Create connection
            $conn = $this->get_connection();

            // Create statement
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::ADD_COURSE);

            // Execute
            $statement->execute([$title, $num, $term, $this->Encode_CourseID($term, $num)]);
            return 0;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }

    /** Update course information
     * @param  String $num       e.g., COMP307
     * @param  String $term      e.g., WINTER2022
     * @param  String $property  choosing between 'title', 'num' and 'term'
     * @param  String $new_value
     * @return int
     * <p> errCode of the update                                                            </p>
     * <ul>
     * <li>  0: Success                                                                    </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * </ul>
     */
    function Update_Course(String $num, String $term, String $property, String $new_value) : int
    {
        // Verify course_num
        $num = strtoupper($num);
        if (empty($num) || !preg_match("/^[A-Z]{4}[0-9]{3}$/", $num))
            return -14; // Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!

        // Verify course_term
        $term = strtoupper($term);
        if(empty($term) || !preg_match("/^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/", $term))
            return -15; // Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected!

        try
        {
            // Create connection
            $conn = $this->get_connection();

            // Create statement
            $statement_library = new SQL_STATEMENTS();

            switch ($property)
            {
                case 'title':
                {
                    $statement = $conn -> prepare($statement_library::UPDATE_COURSE_TITLE);
                    break;
                }
                case 'num':
                {
                    $statement = $conn -> prepare($statement_library::UPDATE_COURSE_NUM);
                    break;
                }
                case 'term':
                {
                    $statement = $conn -> prepare($statement_library::UPDATE_COURSE_TERM);
                    break;
                }
                default:
                {
                    echo "Unknown property: ".$property.PHP_EOL;
                    return -11;
                }
            }
            // Execute
            $statement->execute([$new_value, $this->Encode_CourseID($term, $num)]);

            return $statement->fetchColumn();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }

    /** Count number of courses matching given course_number and course_term
     * @param String $num
     * @param String $term
     * @return int
     * <p> number of courses matching given information (usually 0 or 1 as result) </p>
     * <ul>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * </ul>
     */
    function Count_Course(String $num, String $term): int
    {
        // Verify course_num
        $num = strtoupper($num);
        if (empty($num) || !preg_match("/^[A-Z]{4}[0-9]{3}$/", $num))
            return -14; // Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!

        // Verify course_term
        $term = strtoupper($term);
        if(empty($term) || !preg_match("/^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/", $term))
            return -15; // Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected!

        try
        {
            // Create connection
            $conn = $this->get_connection();

            // Create statement
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::COUNT_COURSE);

            // Execute
            $statement->execute([$num, $term]);

            return $statement->fetchColumn();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }

    /** Delete a user from course registration
     * @param String $userID
     * @param String $role     choosing between "student", "ta" and "instructor"
     * @param String $courseID e.g., WINTER2022_COMP307
     * @return int
     * <p> ErrCode of deletion                                                             </p>
     * <ul>
     * <li>  0: Success                                                                    </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-12: Identity mismatch. ($userID is not one of the $role)                       </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid term format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected!       </li>
     * <li>-16: Course doesn't exist!                                                      </li>
     * </ul>
     * @see CourseManagement::Verify_Identity() for errCode -12
     */
    function Remove_From_Course(String $userID, String $role, String $courseID) : int
    {
        // Verify course existence
        $decode  = $this -> Decode_CourseID($courseID);
        $errCode = $this -> Count_Course($decode['COURSE_NUM'], $decode['TERM_YEAR']);

        if($errCode == 0) return -16;       // Course doesn't exist!
        if($errCode <  0) return $errCode;  // Error with $num or $term

        // Verify identity
        $errCode = $this->Verify_Identity($role, $userID);
        if ($errCode != 0)
            return $errCode;

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = null;
            switch ($role)
            {
                case "student":
                {
                    $statement = $conn->prepare($statement_library::DELETE_STUDENT_COURSE);
                    break;
                }
                case "ta":
                {
                    $statement = $conn->prepare($statement_library::DELETE_TA_COURSE);
                    break;
                }
                case "instructor":
                {
                    $statement = $conn->prepare($statement_library::DELETE_INSTRUCTOR_COURSE);
                    break;
                }
            }

            // Execute
            $statement->execute([$userID, $courseID]);
            return 0;
        }
        catch (Exception $e)
        {
            echo $e -> getMessage();
            return -11;
        }
    }


    /** Register a user to a course
     * @param String $userID
     * @param String $role choosing between "student", "ta" and "instructor"
     * @param String $num  course_number
     * @param String $term course_term
     * @param String $date optional:
     * <ul>
     * <li> $date="null"  : default (NULL for SINCE field)    </li>
     * <li> $date="set"   : set current date.                 </li>
     * <li> $date="Y-m-d" : set given date.(e.g., 2022-04-15) </li>
     * @return int
     * <p> ErrCode of registration                                                         </p>
     * <ul>
     * <li>  0: Success                                                                    </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-12: Identity mismatch. ($userID is not one of the $role)                       </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-16: Course doesn't exist!                                                      </li>
     * <li>-17: Incorrect date format! (Should be Y-m-d)                                   </li>
     * </ul>
     * @see CourseManagement::Verify_Identity() for errCode -12
     */
    function Register_To_Course(String $userID, String $role, String $num, String $term, String $date="null") : int
    {
        // Verify course existence
        $errCode = $this->Count_Course($num, $term);

        if($errCode == 0) return -16;       // Course doesn't exist!
        if($errCode <  0) return $errCode;  // Error with $num or $term

        // Verify identity
        $errCode = $this->Verify_Identity($role, $userID);
        if ($errCode != 0)
            return $errCode;

        // Verify date
        if (DateTime::createFromFormat("Y-m-d", $date) == false)
        {
            if      (strcmp($date, "null") === 0) $date = PDO::PARAM_NULL;
            else if (strcmp($date, "set")  === 0) $date = date("Y-m-d");
            else return -17; // Incorrect date format!
        }

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = null;
            switch ($role)
            {
                case "student":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_STUDENT_COURSE);
                    break;
                }
                case "ta":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_TA_COURSE);
                    break;
                }
                case "instructor":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_INSTRUCTOR_COURSE);
                    break;
                }
            }

            // Execute
            $statement->execute([$userID, $this->Encode_CourseID($term, $num), $date]);
            return 0;
        }
        catch (Exception $e)
        {
            echo $e -> getMessage();
            return -11;
        }

    }


    /**
     * Verify if given user has given identity.
     * @param  String $identity choosing between 'student', 'ta', 'instructor', 'sysop', 'admin'
     * @param  String $userID
     * @return int
     * <p>errCode: Result of verifying                              </p>
     * <ul>
     * <li> 0:  Match                                               </li>
     * <li>-5:  Incorrect userID format: should be 9 digits         </li>
     * <li>-11: Unknown SQL error or unknown property. (See echo)   </li>
     * <li>-12: Mismatch                                            </li>
     * </ul>
     */
    function Verify_Identity(String $identity, String $userID) : int
    {
        // Verify userID
        if (strlen($userID) != 9 || preg_match('#[^0-9]#', $userID))
            return -5; // Incorrect userID format: should be 9 digits

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement = null;

            switch ($identity)
            {
                case 'student' :
                {
                    $statement = $conn->prepare($statement_library::COUNT_USER_STUDENT);
                    break;
                }
                case 'ta' :
                {
                    $statement = $conn->prepare($statement_library::COUNT_USER_TA);
                    break;
                }
                case 'instructor' :
                {
                    $statement = $conn->prepare($statement_library::COUNT_USER_INSTRUCTOR);
                    break;
                }
                case 'sysop' :
                {
                    $statement = $conn->prepare($statement_library::COUNT_USER_SYSOP);
                    break;
                }
                case 'admin' :
                {
                    $statement = $conn->prepare($statement_library::COUNT_USER_ADMIN);
                    break;
                }
                default:
                {
                    echo "Unknown identity: ".$identity.PHP_EOL;
                    return -11;
                }
            }

            $statement->execute([$userID]);

            return $statement->fetchColumn() == 0 ? -12 : 0;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }

    /** Delete a course from database
     * @param String $num  course_number
     * @param String $term course_term
     * @return int
     * <p> ErrCode of deletion                                                             </p>
     * <ul>
     * <li>  0: Success                                                                    </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-16: Course doesn't exist!                                                      </li>
     * </ul>
     */
    function Delete_Course(String $num, String $term) : int
    {
        // Verify course existence
        $errCode = $this->Count_Course($num, $term);

        if($errCode == 0) return -16;       // Course doesn't exist!
        if($errCode <  0) return $errCode;  // Error with $num or $term

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::DELETE_COURSE);

            // Execute
            $statement->execute([$num, $term]);

            return 0;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }

    /** Verify if a user is registered in given course
     * @param String $userID
     * @param String $role  choosing between "student", "ta" and "instructor"
     * @param String $num   course_number
     * @param String $term  course_term
     * @return int
     * <p> ErrCode of registration                                                         </p>
     * <ul>
     * <li>  0: User is registered                                                         </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-12: Identity mismatch. ($userID is not one of the $role)                       </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-16: Course doesn't exist!                                                      </li>
     * <li>-18: User not registered in course!                                             </li>
     * </ul>
     * @see CourseManagement::Verify_Identity() for errCode -12
     */
    function Verify_In_Course(String $userID, String $role, String $num, String $term): int
    {
        // Verify course existence
        $errCode = $this->Count_Course($num, $term);

        if($errCode == 0) return -16;       // Course doesn't exist!
        if($errCode <  0) return $errCode;  // Error with $num or $term

        // Verify identity
        $errCode = $this->Verify_Identity($role, $userID);
        if ($errCode != 0)
            return $errCode;

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = null;
            switch ($role)
            {
                case "student":
                {
                    $statement = $conn->prepare($statement_library::COUNT_STUDENT_COURSE);
                    break;
                }
                case "ta":
                {
                    $statement = $conn->prepare($statement_library::COUNT_TA_COURSE);
                    break;
                }
                case "instructor":
                {
                    $statement = $conn->prepare($statement_library::COUNT_INSTRUCTOR_COURSE);
                    break;
                }
            }

            // Execute
            $statement->execute([$userID, $this->Encode_CourseID($term, $num)]);
            return $statement->fetchColumn() == 0 ? -18 : 0;
        }
        catch (Exception $e)
        {
            echo $e -> getMessage();
            return -11;
        }
    }

    /** Verify if a user is registered in given course
     * @param String $sid    student userID
     * @param String $tid    ta userID
     * @param String $num    course_number
     * @param String $term   course_term
     * @param int    $rating ranged from 0 to 5 inclusive.
     * @param String $msg    message no more than 1024 characters
     * @return int
     * <p> ErrCode of rating                                                               </p>
     * <ul>
     * <li>  0: Success                                                                    </li>
     * <li>-11: Unknown SQL error. (See echo)                                              </li>
     * <li>-12: Identity mismatch. ($userID is not one of the $role)                       </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-16: Course doesn't exist!                                                      </li>
     * <li>-18: User not registered in course!                                             </li>
     * <li>-19: Incorrect rating range : [0, 5] expected!                                 </li>
     * <li>-20: Incorrect message length: should be less than 1024 characters!             </li>
     * </ul>
     * @see CourseManagement::Verify_Identity() for errCode -12
     */
    function Rate_TA(String $sid, String $tid, String $num, String $term, int $rating, String $msg): int
    {

        // Verify if student and TA has registered in course
        $errCode = $this->Verify_In_Course($sid, 'student', $num, $term);
        if ($errCode != 0) return $errCode;
        $errCode = $this->Verify_In_Course($tid, 'ta', $num, $term);
        if ($errCode != 0) return $errCode;

        // Verify rating
        if ($rating < 0 || $rating > 5) return -19; // Incorrect rating range : [0, 5] expected!

        // Verify message
        if (strlen($msg) > 1024) return -20;         // Incorrect message length: should be less than 1024 characters!

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::RATE_TA);

            // Execute
            $statement->execute([$this->Encode_CourseID($term, $num), $sid, $tid, $rating, $msg]);
            return 0;
        }
        catch (Exception $e)
        {
            echo $e -> getMessage();
            return -11;
        }
    }

    /**
     * Add an instructor log to the database
     * @param String $cid      courseID (e.g., WINTER2022_COMP307)
     * @param String $iid      userID of instructor
     * @param String $tid      userID of TA
     * @param String $msg      instructor note on TA (maximum 1024 characters long)
     * @param bool   $wishlist whether TA has been added to professor's wishlist
     * @return int
     * <p> ErrCode of adding log                                                           </p>
     * <ul>
     * <li> 0:  Success                                                                    </li>
     * <li>-5:  Incorrect userID format: should be 9 digits                                </li>
     * <li>-11: Unknown SQL error or unknown property. (See echo)                          </li>
     * <li>-12: tid or iid mismatch                                                        </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-20: Incorrect message length: should be less than 1024 characters!             </li>
     * </ul>
     * @see CourseManagement::Verify_Identity()
     * @see CourseManagement::Count_Course()
     * @see CourseManagement::Decode_CourseID()
     */
    private function Update_TA_Log(String $cid, String $iid, String $tid, String $msg, bool $wishlist): int
    {
        // ID verification
        $errCode = $this->Verify_Identity("instructor", $iid);
        if ($errCode != 0)
            return $errCode; // $iid doesn't match an instructor

        $errCode = $this->Verify_Identity("ta",         $tid);
        if ($errCode != 0)
            return $errCode; // $tid doesn't match a TA

        $decode  = $this-> Decode_CourseID($cid);
        $num     = $decode['COURSE_NUM'];
        $term    = $decode['TERM_YEAR'];
        $errCode = $this->Count_Course($num, $term);

        if ($errCode != 1)
            return $errCode; // $cid doesn't match a course

        // Msg verification
        if (strlen($msg))
            return -20;      // Incorrect message length: should be less than 1024 characters!

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::INSTRUCTOR_LOG);

            // Execute
            $statement->execute([$cid, $iid, $tid, $msg, $wishlist]);
            return 0;
        }
        catch (Exception $e)
        {
            echo $e -> getMessage();
            return -11;
        }
    }

    /**
     * Add an instructor log to the database
     * @param String $cid      courseID (e.g., WINTER2022_COMP307)
     * @param String $iid      userID of instructor
     * @param String $tid      userID of TA
     * @param String $msg      instructor note on TA (maximum 1024 characters long)
     * @param bool   $wishlist whether TA has been added to professor's wishlist
     * @return int
     * <p> ErrCode of adding log                                                           </p>
     * <ul>
     * <li> 0:  Success                                                                    </li>
     * <li>-5:  Incorrect userID format: should be 9 digits                                </li>
     * <li>-11: Unknown SQL error or unknown property. (See echo)                          </li>
     * <li>-12: tid or iid mismatch                                                        </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-20: Incorrect message length: should be less than 1024 characters!             </li>
     * </ul>
     * @see CourseManagement::Verify_Identity()
     * @see CourseManagement::Count_Course()
     * @see CourseManagement::Decode_CourseID()
     */
    function Instructor_Log(String $cid, String $iid, String $tid, String $msg, bool $wishlist = false) : int
    {
        return $this->Update_TA_Log($cid, $iid, $tid, $msg, $wishlist);
    }

    /**
     * Add an instructor log to the database
     * @param String $cid      courseID (e.g., WINTER2022_COMP307)
     * @param String $iid      userID of instructor
     * @param String $tid      userID of TA
     * @param String $msg      instructor note on TA (maximum 1024 characters long)
     * @param bool   $wishlist whether TA has been added to professor's wishlist
     * @return int
     * <p> ErrCode of adding log                                                           </p>
     * <ul>
     * <li> 0:  Success                                                                    </li>
     * <li>-5:  Incorrect userID format: should be 9 digits                                </li>
     * <li>-11: Unknown SQL error or unknown property. (See echo)                          </li>
     * <li>-12: tid or iid mismatch                                                        </li>
     * <li>-14: Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!                  </li>
     * <li>-15: Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected! </li>
     * <li>-20: Incorrect message length: should be less than 1024 characters!             </li>
     * </ul>
     * @see CourseManagement::Verify_Identity()
     * @see CourseManagement::Count_Course()
     * @see CourseManagement::Decode_CourseID()
     */
    function Update_Wishlist(String $cid, String $iid, String $tid, String $msg = "Add to wishlist", bool $wishlist = false): int
    {
        return $this->Update_TA_Log($cid, $iid, $tid, $msg, $wishlist);
    }
}