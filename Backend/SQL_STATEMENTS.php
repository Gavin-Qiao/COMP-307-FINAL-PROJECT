<?php

class SQL_STATEMENTS
{
    public const DATABASE_NAME = "TA_Management_Website.db";
    public const TEST_DATABASE = "toy.db";

    /*
    ==========================================
       DEBUGGING
    ==========================================
     */
    /** Whether DEBUG_MODE is on*/
    public const DEBUG_MODE  = true;
    /**Testing only: Insert test*/
    public const INSERT_TEST = "INSERT INTO TEST(id, name) VALUES(?, ?);";
    public const DELETE_TEST = "DELETE FROM TEST WHERE id = ?;";
    public const SELECT_TEST = "SELECT * FROM TEST;";

    /*
    ==========================================
       TABLE: USER
    ==========================================
     */

    public const COUNT_USER_EMAIL        = "SELECT COUNT(*) FROM USER WHERE EMAIL     = ?;";

    public const COUNT_USER_NAME         = "SELECT COUNT(*) FROM USER WHERE USER_NAME = ?;";

    public const COUNT_USER_ID           = "SELECT COUNT(*) FROM USER WHERE USER_ID   = ?;";

    public const REGISTER_USER           = "INSERT INTO 
                                            USER(USER_NAME, PASSWORD, USER_ID, FIRST_NAME, LAST_NAME, EMAIL)
                                            VALUES(?, ?, ?, ?, ?, ?);";

    public const VERIFY_LOGIN            = "SELECT COUNT(*) FROM USER WHERE USER_NAME = ? AND PASSWORD = ?;";

    public const GET_USER_INFO           = "SELECT USER_NAME, USER_ID, FIRST_NAME, LAST_NAME, EMAIL 
                                            FROM USER ORDER BY FIRST_NAME;";

    public const UPDATE_ID               = "UPDATE USER SET USER_ID    = ? WHERE USER_ID = ?;";

    public const UPDATE_USERNAME         = "UPDATE USER SET USER_NAME  = ? WHERE USER_ID = ?;";

    public const UPDATE_PASSWORD         = "UPDATE USER SET PASSWORD   = ? WHERE USER_ID = ?;";

    public const UPDATE_FIRSTNAME        = "UPDATE USER SET FIRST_NAME = ? WHERE USER_ID = ?;";

    public const UPDATE_LASTNAME         = "UPDATE USER SET LAST_NAME  = ? WHERE USER_ID = ?;";

    public const UPDATE_EMAIL            = "UPDATE USER SET EMAIL      = ? WHERE USER_ID = ?;";

    public const COUNT_USER_STUDENT      = "SELECT COUNT(*) FROM USER U JOIN STUDENT    S on U.USER_ID = S.ID WHERE S.ID = ?;";

    public const COUNT_USER_TA           = "SELECT COUNT(*) FROM USER U JOIN TA         T on U.USER_ID = T.ID WHERE T.ID = ?;";

    public const COUNT_USER_INSTRUCTOR   = "SELECT COUNT(*) FROM USER U JOIN INSTRUCTOR I on U.USER_ID = I.ID WHERE I.ID = ?;";

    public const COUNT_USER_SYSOP        = "SELECT COUNT(*) FROM USER U JOIN SYSOP      S on U.USER_ID = S.ID WHERE S.ID = ?;";

    public const COUNT_USER_ADMIN        = "SELECT COUNT(*) FROM USER U JOIN ADMIN      A on U.USER_ID = A.ID WHERE A.ID = ?;";

    public const REGISTER_AS_STUDENT     = "INSERT INTO STUDENT   (ID) VALUES (?)";

    public const REGISTER_AS_TA          = "INSERT INTO TA        (ID) VALUES (?)";

    public const REGISTER_AS_INSTRUCTOR  = "INSERT INTO INSTRUCTOR(ID) VALUES (?)";

    public const REGISTER_AS_SYSOP       = "INSERT INTO SYSOP     (ID) VALUES (?);";

    public const REGISTER_AS_ADMIN       = "INSERT INTO ADMIN     (ID) VALUES (?);";

    public const DELETE_USER             = "DELETE FROM USER WHERE USER_ID = ?;";

    /*
    ============================================================
       TABLE: COURSE // TODO: FIX EVERYTHING RELATED TO COURSE
    ============================================================
    */

    public const FIND_COURSE                   = "SELECT CID FROM REGISTER_STUDENT_COURSE    WHERE SID = ? UNION
                                                  SELECT CID FROM REGISTER_TA_COURSE         WHERE TID = ? UNION
                                                  SELECT CID FROM REGISTER_INSTRUCTOR_COURSE WHERE IID = ?;";

    public const GET_COURSE_TITLE              = "SELECT COURSE_TITLE FROM COURSE WHERE COURSE_ID = ?;";

    public const FIND_STUDENT_COURSE           = "SELECT CID FROM REGISTER_STUDENT_COURSE WHERE SID = ?;";

    public const FIND_TA_COURSE                = "SELECT CID, SINCE, HOURS, NOTE 
                                                  FROM REGISTER_TA_COURSE WHERE TID = ?;";

    public const FIND_INSTRUCTOR_COURSE        = "SELECT CID FROM REGISTER_INSTRUCTOR_COURSE WHERE IID = ?;";

    public const ADD_COURSE                    = "INSERT INTO COURSE(COURSE_TITLE, COURSE_NUM, TERM_YEAR) VALUES (?, ?, ?);";

    public const UPDATE_COURSE_TITLE           = "UPDATE COURSE SET COURSE_TITLE = ? WHERE COURSE_ID = ?;";

    public const UPDATE_COURSE_NUM             = "UPDATE COURSE SET COURSE_NUM   = ? WHERE COURSE_ID = ?;";

    public const UPDATE_COURSE_TERM            = "UPDATE COURSE SET TERM_YEAR    = ? WHERE COURSE_ID = ?;";

    public const DELETE_COURSE                 = "DELETE FROM COURSE WHERE COURSE_NUM = ? AND TERM_YEAR = ?;";

    public const COUNT_COURSE                  = "SELECT COUNT(*) FROM COURSE WHERE COURSE_NUM = ? AND TERM_YEAR = ?;";

    public const REGISTER_STUDENT_COURSE       = "INSERT INTO REGISTER_STUDENT_COURSE   (SID, CID, SINCE) VALUES (?, ?, ?);";

    public const REGISTER_TA_COURSE            = "INSERT INTO REGISTER_TA_COURSE        (TID, CID, SINCE) VALUES (?, ?, ?);";

    public const REGISTER_INSTRUCTOR_COURSE    = "INSERT INTO REGISTER_INSTRUCTOR_COURSE(IID, CID, SINCE) VALUES (?, ?, ?);";

    public const COUNT_STUDENT_COURSE          = "SELECT COUNT(*) FROM REGISTER_STUDENT_COURSE    WHERE SID = ? AND CID = ?;";

    public const COUNT_TA_COURSE               = "SELECT COUNT(*) FROM REGISTER_TA_COURSE         WHERE TID = ? AND CID = ?;";

    public const COUNT_INSTRUCTOR_COURSE       = "SELECT COUNT(*) FROM REGISTER_INSTRUCTOR_COURSE WHERE IID = ? AND CID = ?;";

    public const RATE_TA                       = "INSERT INTO RATE   (CID, SID, TID, RATING, MSG)    VALUES (?, ?, ?, ?, ?)";

    public const INSTRUCTOR_LOG                = "INSERT INTO TA_LOG (CID, IID, TID, NOTE, WISHLIST) VALUES (?, ?, ?, ?, ?);";

}