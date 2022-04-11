<?php

class SQL_STATEMENTS
{
    public const DATABASE_NAME = "login.db";
    public const TEST_DATABASE = "test.db";
    /*
    ==================
       DEBUGGING
    ==================
     */
    /** Whether DEBUG_MODE is on*/
    public const DEBUG_MODE  = true;
    /**Testing only: Insert test*/
    public const INSERT_TEST = "INSERT INTO TEST(id, name) VALUES(?, ?);";
    public const DELETE_TEST = "DELETE FROM TEST WHERE id = ?;";
    public const SELECT_TEST = "SELECT * FROM TEST;";

    /*
    ==================
       TABLE: USER
    ==================
     */

    public const FIND_USER_BY_ID       = "SELECT * FROM USER WHERE USER_ID = '?';";
    public const FIND_USER_BY_USERNAME = "SELECT * FROM USER WHERE USER_NAME = '?';";
    public const FIND_USER_BY_EMAIL    = "SELECT * FROM USER WHERE EMAIL = '?';";
    public const REGISTER_USER         = "INSERT INTO " .
                                         "USER(USER_ID, USER_NAME, PASSWORD, FIRST_NAME, LAST_NAME, EMAIL) " .
                                         "VALUES(?, ?, ?, ?, ?, ?);";
}