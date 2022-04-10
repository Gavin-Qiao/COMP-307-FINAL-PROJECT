package Backend;
import java.sql.Connection;
import java.util.HashSet;

public final class DatabaseStatement
{
    /*
    ==================
       DEBUGGING
    ==================
     */
    /** Whether DEBUG_MODE is on*/
    static final boolean DEBUG_MODE            = true;

    /**Testing only: Insert test*/
    static final String  INSERT_TEST           = "INSERT INTO TEST(id, name) VALUES(?, ?);";

    /*
    ==================
       TABLE: USER
    ==================
     */
    static final String FIND_USER_BY_ID       = "SELECT * FROM USER WHERE USER_ID = '?';";
    static final String FIND_USER_BY_USERNAME = "SELECT * FROM USER WHERE USER_NAME = '?';";
    static final String FIND_USER_BY_EMAIL    = "SELECT * FROM USER WHERE EMAIL = '?';";
    static final String REGISTER_USER         = "INSERT INTO " +
                                                "USER(USER_ID, USER_NAME, PASSWORD, FIRST_NAME, LAST_NAME, EMAIL) " +
                                                "VALUES(?, ?, ?, ?, ?, ?);";

}
