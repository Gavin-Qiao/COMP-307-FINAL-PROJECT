<?php

class DatabaseDriver
{
    private string $database_path;
    private PDO    $connection;

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
     */
    function errCode_decoder(int $errCode)
    {
        echo match ($errCode)
        {
             0      => "Success",
            -1      => "Incorrect username length ([1, 30] expected)",
            -2      => "Username already taken/doesn't exist!",
            -3      => "Empty password",
            -4      => "Incorrect password format",
            -5      => "Incorrect userID format: should be 9 digits",
            -6      => "userID already taken/doesn't exist!",
            -7      => "Empty first/last name",
            -8      => "First/last name too long! (greater than 256 characters)",
            -9      => "Incorrect email length ([4, 320] expected)",
            -10     => "Email already taken/doesn't exist!",
            -11     => "Unknown SQL error or unknown property. (See echo)",
            -12     => "Invalid query pair",
            -13     => "Invalid course_title length: ]0, 64] expected!",
            -14     => "Invalid course_num format: /^[A-Z]{4}[0-9]{3}$/ expected!",
            -15     => "Invalid course_num format: /^((WINTER)|(SUMMER)|(FALL))[0-9]{4}/ expected!",
            -16     => "Course not found/already exists!",
            -17     => "Incorrect date format!",
            -18     => "User not registered in course!",
            -19     => "Incorrect rating range : [0, 5] expected!",
            -20     => "Incorrect message length: should be less than 1024 characters!",
            -21     => "Incorrect education choice: should be between 'grad', 'ugrad' and 'other'",
            -22     => "Location too long! Should be less than 256 characters",
            -23     => "Phone number too long! Should be less than 30 characters",
            -24     => "Negative degree/hour",
            -25     => "Note too long! Should be less than 1024 characters",
            default => "Unknown Error Code!",
        };
        echo PHP_EOL;
    }

}