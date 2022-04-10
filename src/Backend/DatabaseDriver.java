package Backend;

import java.sql.*;
import static Backend.DatabaseStatement.*;

public abstract class DatabaseDriver
{
    private final String database;

    public DatabaseDriver(String database_path)
    {
        this.database = database_path;
    }


    /**
     *  Create connection to the database defined in the driver
     *  @return Conenction
     * */
    protected Connection createConnection() throws SQLException
    {
        // Connect to SQLite database:
        String url  = "jdbc:sqlite:" + database;

        // TODO: DEBUG only
        if(DEBUG_MODE) System.out.println(url);

        return DriverManager.getConnection(url);
    }

    /**
     *  Close connection to the database defined in the driver
     * */
    protected void closeConnection(Connection conn) throws SQLException
    {
        conn.close();
    }

    /**
     *  Return the size of query result
     *  @param rs java.sql.ResultSet
     *  @return size of query result (0 if rs == null)
     * */
    protected int getResultSize(ResultSet rs) throws SQLException
    {
        // Input protection
        if (rs == null) return 0;

        // Move cursor to the last row
        rs.last();

        // Return row number
        return rs.getRow();
    }
}
