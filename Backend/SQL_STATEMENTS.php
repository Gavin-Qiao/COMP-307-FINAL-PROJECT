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

    public const FIND_USER_EMAIL       = "SELECT COUNT(*) FROM USER WHERE EMAIL     = ?;";

    public const FIND_USER_NAME        = "SELECT COUNT(*) FROM USER WHERE USER_NAME = ?;";

    public const FIND_USER_ID          = "SELECT COUNT(*) FROM USER WHERE USER_ID   = ?;";

    public const REGISTER_USER         = "INSERT INTO 
                                          USER(USER_NAME, PASSWORD, USER_ID, FIRST_NAME, LAST_NAME, EMAIL)
                                          VALUES(?, ?, ?, ?, ?, ?);";

    public const VERIFY_LOGIN          = "SELECT COUNT(*) FROM USER WHERE USER_NAME = ? AND PASSWORD = ?;";

    public const GET_USER_INFO         = "SELECT USER_NAME, USER_ID, FIRST_NAME, LAST_NAME, EMAIL 
                                          FROM USER ORDER BY FIRST_NAME;";

    public const UPDATE_ID             = "UPDATE USER SET USER_ID    = ? WHERE USER_ID = ?;";

    public const UPDATE_USERNAME       = "UPDATE USER SET USER_NAME  = ? WHERE USER_ID = ?;";

    public const UPDATE_PASSWORD       = "UPDATE USER SET PASSWORD   = ? WHERE USER_ID = ?;";

    public const UPDATE_FIRSTNAME      = "UPDATE USER SET FIRST_NAME = ? WHERE USER_ID = ?;";

    public const UPDATE_LASTNAME       = "UPDATE USER SET LAST_NAME  = ? WHERE USER_ID = ?;";

    public const UPDATE_EMAIL          = "UPDATE USER SET EMAIL      = ? WHERE USER_ID = ?;";

}