<?php

require_once 'DatabaseDriver.php';
require_once 'SQL_STATEMENTS.php';

class UserManagement extends DatabaseDriver
{
    /**
     *  Use to check if a password is valid, i.e., the password contains ONLY [a-z0-9A-Z]
     *  <ul>
     *  <li>Contains at lease:       </li>
     *  <li>1 lowercase letter       </li>
     *  <li>1 uppercase letter       </li>
     *  <li>1 digit                  </li>
     *  <li>8 characters             </li>
     *  <li><a href="https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_password_val">
     *     Javascript Example
     *  </li>
     * @param $password - input password
     * @return int - ErrorCode: <ul>
     * <li>-3 : empty password  </li>
     * <li>-4 : incorrect format</li>
     * </ul>
     * */
    function Password_Validation(String $password): int
    {
        if (empty($password))
            return -3;

        // Password should have length at least 8
        if (strlen($password) < 8)
           return -4;

        // Password should contain at least one lowercase letter
        $contain_lower = preg_match('/[a-z]/', $password);

        // Password should contain at least one uppercase letter
        $contain_upper = preg_match('/[A-Z]/', $password);

        // Password should contain at least one digit
        $contain_digit = preg_match('/\d/', $password);

        // Password should not contain special characters
        $contain_spec  = preg_match('/[^a-zA-Z\d]/', $password);

        if ($contain_lower && $contain_upper && $contain_digit && !$contain_spec) return 0;
        else return -4;
    }

    /**
     *  Using SHA-256 to encrypt password
     * @param $password - Input password
     * @return String of encrypted password by SHA-256 </br>
     * <p><a href="https://en.wikipedia.org/wiki/SHA-2"> SHA-2 introduction </a></p>
     * */
    function Password_Encryption(String $password): String
    {
        return hash("sha256", $password);
    }

    /**
     * Count number of users matching given property and value
     * @param String $property choosing between "email", "id" and "username"
     * @param String $value value of input property
     * @return int
     * <p> number of users matching given property and value    </p>
     * <p> -11 : Incorrect $property value or unknown SQL error </p>
     * @see SQL_STATEMENTS
     */
    function Count_User(String $property, String $value): int
    {
        try
        {
            // Get connection
            $conn  = $this -> get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();

            $statement = null;

            switch ($property)
            {
                case "email":
                {
                    $statement = $conn -> prepare($statement_library::COUNT_USER_EMAIL);
                    break;
                }

                case "id":
                {
                    $statement = $conn -> prepare($statement_library::COUNT_USER_ID);
                    break;
                }
                case "username":
                {
                    $statement = $conn -> prepare($statement_library::COUNT_USER_NAME);
                    break;
                }
                default:
                {
                    echo "Unknown property: ".$property.PHP_EOL;
                    return -11;
                }
            }

            $statement        -> execute([$value]);
            return $statement -> fetchColumn();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }

    /** TODO: Discuss tolerance for special character (e.g., é)
     * Reformat input name by putting the first letter uppercase and others lower case
     * @param String $name input name (might be first or last name)
     * @return String reformatted name ("" if input $name is empty)
     * */
    function Name_Reformat(String $name) : String
    {
        if (empty($name)) return $name;

        $name[0] = strtoupper($name[0]);
        for ($i = 1; $i < strlen($name); $i++)
        {
            $name[$i] = strtolower($name[$i]);
        }

        return $name;
    }

    /** TODO: Testing
     *  Register a user to the system with full user information.
     * @param String $username  read from user input: should be unique in db
     * @param String $password  read from user input after validation (enter two times, length, lower/uppercase, etc.)
     * @param String $userID    read from user after verification of (userName, pw) pair: should be unique in db
     * @param String $firstName read from user after verification of (userName, pw) pair
     * @param String $lastName  read from user after verification of (userName, pw) pair
     * @param String $email     read from user after verification of (userName, pw) pair: should be unique in db
     * @return int
     * <p>errCode: Status of registration</p>
     * <ul>
     * <li> 0: Success                                                     </li>
     * <li>-1: Incorrect username length ([1, 30] expected)                </li>
     * <li>-2: Username already taken!                                     </li>
     * <li>-3: Empty password                                              </li>
     * <li>-4: Incorrect password format                                   </li>
     * <li>-5: Incorrect userID format: should be 9 digits                 </li>
     * <li>-6: userID already taken!                                       </li>
     * <li>-7: Empty first/last name                                       </li>
     * <li>-8: First/last name too long! (greater than 256 characters)     </li>
     * <li>-9: Incorrect email length ([4, 320] expected)                  </li>
     * <li>-10: Email already taken!                                       </li>
     * <li>-11: Unknown SQL error. (See echo)                              </li>
     * </ul>
     * @see UserManagement::Password_Validation()
     */
    function Register_User(String $username,
                           String $password,
                           String $userID,
                           String $firstName,
                           String $lastName,
                           String $email) : int
    {
        // Verify username
        if (empty($username) || strlen($username) > 30)
            return -1; // Incorrect username length ([1, 30] expected)
        if ($this->Count_User("username", $username) != 0)
            return -2; // Username already taken

        // Verify password
        $password_errCode = $this->Password_Validation($password);
        if ($password_errCode != 0) return $password_errCode; // Invalid password

        // Verify userID TODO:Design choice
        if (strlen($userID) != 9 || preg_match('#[^0-9]#', $userID))
            return -5; // Incorrect userID format: should be 9 digits
        if ($this->Count_User("id", $userID) != 0)
            return -6; // UserID already taken

        // Verify first/last name
        if (empty($firstName) || empty($lastName))
            return -7; // Empty first/last name
        if (strlen($firstName) > 256 || strlen($lastName) > 256)
            return -8; // First/last name too long! (greater than 256 characters)

        // Verify email
        if (empty($email) || strlen($email) < 3 || strlen($email) > 320)
            return -9; // Incorrect email length ([4, 320] expected)
        if ($this->Count_User("email", $email) != 0)
            return -10; // Email already taken!

        try
        {
            // Get connection
            $conn = $this->get_connection();

            // Get statement library
            $statement_library = new SQL_STATEMENTS();

            // Prepare statement
            $statement = $conn -> prepare($statement_library::REGISTER_USER);

            // Encrypt Password
            $password = $this -> Password_Encryption($password);

            // Reformat names
            $firstName = $this -> Name_Reformat($firstName);
            $lastName  = $this -> Name_Reformat($lastName );

            // Perform update
            $statement -> execute([$username, $password, $userID, $firstName, $lastName, $email]);
        }
        catch (PDOException $e)
        {
            echo $e->getMessage();
            return -11;
        }
        return 0;
    }

    /**
     * @param String $username read from user input
     * @param String $password read from user input
     * @return int
     * <p>errCode: Status of login</p>
     * <ul>
     * <li> 0: Success                                      </li>
     * <li>-1: Incorrect username length ([1, 30] expected) </li>
     * <li>-2: Username already taken!                      </li>
     * <li>-3: Empty password                               </li>
     * <li>-4: Incorrect password format                    </li>
     * <li>-11: Unknown SQL error. (See echo)               </li>
     * <li>-12: Invalid username/password pair              </li>
     * </ul>
     */
    function Login_User(String $username, String $password) : int
    {
        if (empty($username) || strlen($username) > 30)
            return -1; // Incorrect username length ([1, 30] expected)
        if ($this->Count_User("username", $username) == 0)
            return -2; // Username not found in database

        $password_errCode = $this->Password_Validation($password);
        if ($password_errCode != 0) return $password_errCode; // Invalid password

        try
        {
            // Create connection
            $conn = $this->get_connection();

            // Prepare statements
            $statement_library = new SQL_STATEMENTS();
            $statement = $conn->prepare($statement_library::VERIFY_LOGIN);

            // Encrypt password
            $password = $this->Password_Encryption($password);

            // Query
            $statement -> execute([$username, $password]);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }

        return $statement->fetchColumn() == 0 ? -12 : 0;
    }

    /**
     * Get information about all users in the system
     * @return array
     * <p>['USER_ID', 'USER_NAME', 'FIRST_NAME', 'LAST_NAME', 'EMAIL']                </p>
     * <p>Empty array ([]) indicates either no records found or unknown SQL exception </p>
     */
    function Get_All_Users() : array
    {
        try
        {
            // Create connection
            $conn = $this -> get_connection();

            // Statement
            $statement_library = new SQL_STATEMENTS();
            $statement = $conn->query($statement_library::GET_USER_INFO);
            $USERS = [];
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC))
            {
                $USERS[] =
                [
                    'USER_ID'    => $row['USER_ID'   ],
                    'USER_NAME'  => $row['USER_NAME' ],
                    'FIRST_NAME' => $row['FIRST_NAME'],
                    'LAST_NAME'  => $row['LAST_NAME' ],
                    'EMAIL'      => $row['EMAIL'     ],
                ];
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return [];
        }
        return $USERS;
    }

    /**
     * Update a user's information given its id
     * @param String $userID    UserID to uniquely identify a user
     * @param String $property  Property of the user to update, choosing between
     *                          "userID", "username", "password", "firstName", "lastName"
     *                          and "email".
     * @param String $new_value The new value of given property
     * @return int
     * <p>errCode: Status of update</p>
     * <ul>
     * <li> 0: Success                                                     </li>
     * <li>-1: Incorrect username length ([1, 30] expected)                </li>
     * <li>-2: Username already taken!                                     </li>
     * <li>-3: Empty password                                              </li>
     * <li>-4: Incorrect password format                                   </li>
     * <li>-5: Incorrect userID format: should be 9 digits                 </li>
     * <li>-6: userID already taken!                                       </li>
     * <li>-7: Empty first/last name                                       </li>
     * <li>-8: First/last name too long! (greater than 256 characters)     </li>
     * <li>-9: Incorrect email length ([4, 320] expected)                  </li>
     * <li>-10: Email already taken!                                       </li>
     * <li>-11: Unknown SQL error or unknown property. (See echo)          </li>
     * </ul>
     * @see UserManagement::Password_Validation()
     */
    function Update_User(String $userID, String $property, String $new_value): int
    {
        // Verify userID
        if ($this->Count_User("id", $userID) == 0)
            return -6; // UserID doesn't exist!

        try
        {
            // Get connection
            $conn  = $this -> get_connection();

            // Get statements
            $statement_library = new SQL_STATEMENTS();

            $statement = null;

            switch ($property)
            {
                case "userID":
                {
                    if (strlen($new_value) != 9 || preg_match('#[^0-9]#', $new_value))
                        return -5; // Incorrect userID format: should be 9 digits
                    if ($this -> Count_User("id", $new_value) != 0)
                        return -6; // UserID already taken

                    $statement = $conn -> prepare($statement_library::UPDATE_ID);
                    break;
                }
                case "username":
                {
                    // Verify username
                    if (empty($new_value) || strlen($new_value) > 30)
                        return -1; // Incorrect username length ([1, 30] expected)
                    if ($this->Count_User("username", $new_value) != 0)
                        return -2; // Username already taken

                    $statement = $conn -> prepare($statement_library::UPDATE_USERNAME);
                    break;
                }
                case "password":
                {
                    // Verify password
                    $password_errCode = $this->Password_Validation($new_value);
                    if ($password_errCode != 0) return $password_errCode; // Invalid password

                    $new_value = $this -> Password_Encryption($new_value);
                    $statement = $conn -> prepare($statement_library::UPDATE_PASSWORD);
                    break;
                }
                case "firstName":
                {
                    // Verify first name
                    if (empty($new_value))
                        return -7; // Empty first name
                    if (strlen($new_value) > 256)
                        return -8; // First name too long! (greater than 256 characters)

                    $new_value = $this -> Name_Reformat($new_value);
                    $statement = $conn -> prepare($statement_library::UPDATE_FIRSTNAME);
                    break;
                }
                case "lastName":
                {
                    // Verify last name
                    if (empty($new_value))
                        return -7; // Empty last name
                    if (strlen($new_value) > 256)
                        return -8; // Last name too long! (greater than 256 characters)

                    $new_value = $this -> Name_Reformat($new_value);
                    $statement = $conn -> prepare($statement_library::UPDATE_LASTNAME);
                    break;
                }
                case "email":
                {
                    // Verify email
                    if (empty($new_value) || strlen($new_value) < 3 || strlen($new_value) > 320)
                        return -9; // Incorrect email length ([4, 320] expected)
                    if ($this->Count_User("email", $new_value) != 0)
                        return -10; // Email already taken!

                    $statement = $conn -> prepare($statement_library::UPDATE_EMAIL);
                    break;
                }
                default :
                {
                    echo "Unknown property for update operation: ". $property . " choosing between userID, username, 
                    password, firstName, lastName and email.\n";
                    return -11;
                }
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }

        $statement->execute([$new_value, $userID]);
        return 0;
    }

    /** TODO: MODIFY ACCORDING TO TA, STUDENT AND INSTRUCTOR TABLE
     * Verify if given user has given identity.
     * @param  String $role choosing between "student", "ta", "instructor", "sysop" and "admin"
     * @param  String $userID
     * @return int
     * <p>errCode: Result of registration                                                </p>
     * <ul>
     * <li> 0:  Success                                                                  </li>
     * <li>-5:  Incorrect userID format: should be 9 digits                              </li>
     * <li>-6:  Already registered as role!                                              </li>
     * <li>-8:  Supervisor name too long!                                                </li>
     * <li>-11: Unknown SQL error or unknown property. (See echo)                        </li>
     * </ul>
     */
    function Register_As(String $role, String $userID) : int
    {
        try
        {
            // Verify if already registered
            $cmgt    = new CourseManagement($this->get_database_path());
            $errCode = $cmgt->Verify_Identity($role, $userID);
            if ($errCode == 0)   return -6;       // Already registered!
            if ($errCode != -12) return $errCode; // Some error occurred
            unset($cmgt);

            // Create connection
            $conn = $this->get_connection();

            // Create statement
            $statement_library = new SQL_STATEMENTS();
            $statement         = null;

            switch ($role)
            {
                case "student":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_AS_STUDENT);
                    break;
                }
                case "ta":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_AS_TA);
                    break;
                }
                case "instructor":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_AS_INSTRUCTOR);
                    break;
                }
                case "sysop":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_AS_SYSOP);
                    break;
                }
                case "admin":
                {
                    $statement = $conn->prepare($statement_library::REGISTER_AS_ADMIN);
                    break;
                }
                default:
                {
                    echo "Unknown role: ".$role.PHP_EOL;
                    return -11;
                }
            }

            $statement->execute([$userID]);
            return 0;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }


    /**
     * Delete a user from USER (and from related tables)
     * @param  String $userID
     * @return int
     * <p>errCode: Result of verifying                              </p>
     * <ul>
     * <li> 0:  Success                                             </li>
     * <li>-5:  Incorrect userID format: should be 9 digits         </li>
     * <li>-6:  UserID doesn't exist!                               </li>
     * <li>-11: Unknown SQL error. (See echo)                       </li>
     * </ul>
     */
    function Delete_User(string $userID): int
    {
        // Verify userID
        if (strlen($userID) != 9 || preg_match('#[^0-9]#', $userID))
            return -5; // Incorrect userID format: should be 9 digits
        if ($this->Count_User("id", $userID) == 0)
            return -6; // UserID doesn't exist!

        try
        {
            // Create connection
            $conn = $this->get_connection();

            // Create statement
            $statement_library = new SQL_STATEMENTS();
            $statement         = $conn->prepare($statement_library::DELETE_USER);

            // Execute
            $statement->execute([$userID]);

            return 0;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            return -11;
        }
    }
}