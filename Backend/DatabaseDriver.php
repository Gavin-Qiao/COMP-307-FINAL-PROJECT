<?php

class DatabaseDriver
{
    private string $database_path;
    function __construct($database_path)
    {
        $this->database_path = $database_path;
    }

    function set_path($database_path)
    {
        $this->database_path = $database_path;
    }

    //TODO: Change to protected
    /**
     *  Create connection to the database defined in the driver
     *  @return PDO
     * */
    public function createConnection(): PDO
    {
        return new PDO("sqlite:".$this->database_path);
    }

}