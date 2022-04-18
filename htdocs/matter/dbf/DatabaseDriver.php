<?php

class DatabaseDriver
{
    private $database_path;
    private $connection;

    function __construct($database_path)
    {
        $this -> database_path = $database_path;
        $this -> connection    = $this->createConnection();

        // Turn on foreign key pragma
        $this->connection->exec("PRAGMA foreign_keys = ON;");
    }

    function set_path($database_path)
    {
        $this->database_path = $database_path;
    }

    /**
     *  Create connection to the database defined in the driver
     *  @return PDO
     * */
    private function createConnection(): PDO
    {
        return new PDO("sqlite:".$this->database_path);
    }

    /**
     * @return PDO Connection to the database
     */
    public function get_connection(): PDO
    {
        return $this->connection;
    }

    /**
     * @return String path to the current database
    */
    public function get_database_path() : String
    {
        return $this->database_path;
    }


    /** TODO: CHANGE BACK TO SWITCH (MATCH NOT SUPPORTED ON MIMI)
     * Debug tool to decode error code messages
     * @param int $errCode
     * @return string error message
     */
    function errCode_decoder(int $errCode): string
    {
        switch ($errCode)
        {
            case  0      : return "Success"                                                                   .PHP_EOL;
            case -1      : return "Incorrect username length ([1, 30] expected)"                              .PHP_EOL;
            case -2      : return "Username already taken/doesn't exist!"                                     .PHP_EOL;
            case -3      : return "Empty password"                                                            .PHP_EOL;
            case -4      : return "Incorrect password format"                                                 .PHP_EOL;
            case -5      : return "Incorrect userID format: should be 9 digits"                               .PHP_EOL;
            case -6      : return "userID already taken/doesn't exist!"                                       .PHP_EOL;
            case -7      : return "Empty first/last name"                                                     .PHP_EOL;
            case -8      : return "First/last name too long! (greater than 256 characters)"                   .PHP_EOL;
            case -9      : return "Incorrect email length ([4, 320] expected)"                                .PHP_EOL;
            case -10     : return "Email already taken/doesn't exist!"                                        .PHP_EOL;
            case -11     : return "Unknown SQL error or unknown property. (See echo)"                         .PHP_EOL;
            case -12     : return "Invalid query pair"                                                        .PHP_EOL;
            case -13     : return "Invalid course_title length: ]0, 64] expected!"                            .PHP_EOL;
            case -14     : return "Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!"                 .PHP_EOL;
            case -15     : return "Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected!".PHP_EOL;
            case -16     : return "Course not found/already exists!"                                          .PHP_EOL;
            case -17     : return "Incorrect date format!"                                                    .PHP_EOL;
            case -18     : return "User not registered in course!"                                            .PHP_EOL;
            case -19     : return "Incorrect rating range : [0, 5] expected!"                                 .PHP_EOL;
            case -20     : return "Incorrect message length: should be less than 1024 characters!"            .PHP_EOL;
            case -21     : return "Incorrect education choice: should be between 'grad', 'ugrad' and 'other'" .PHP_EOL;
            case -22     : return "Location too long! Should be less than 256 characters"                     .PHP_EOL;
            case -23     : return "Phone number too long! Should be less than 30 characters"                  .PHP_EOL;
            case -24     : return "Incorrect degree/hour value: should be non-negative integer"               .PHP_EOL;
            case -25     : return "Note too long! Should be less than 1024 characters"                        .PHP_EOL;
            case -26     : return "Incorrect boolean value!"                                                  .PHP_EOL;
            case -27     : return "Office Hours information too long! Should be less than 64 characters."     .PHP_EOL;
            case -28     : return "Duties description too long! Should be less than 256 characters."          .PHP_EOL;
            default      : return "Unknown Error Code!"                                                       .PHP_EOL;
        }
    }

}