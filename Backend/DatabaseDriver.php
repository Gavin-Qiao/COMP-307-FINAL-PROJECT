<?php

class DatabaseDriver
{
    private string $database_path;
    private PDO    $connection;

    function __construct($database_path)
    {
        $this -> database_path = "sqlite:".$database_path;
        $this -> connection    = $this->createConnection();
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
        return new PDO($this->database_path);
    }

    /**
     * @return PDO Connection to the database
     */
    public function get_connection(): PDO
    {
        return $this->connection;
    }

}