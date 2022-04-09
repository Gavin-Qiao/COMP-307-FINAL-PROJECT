import java.sql.*;

public class Driver
{
    public static void main(String[] args)
    {
        String url = "jdbc::sqlite:";

        try
        {
            Connection conn = DriverManager.getConnection(url);
        }
        catch (SQLException e)
        {
            e.printStackTrace();
        }
    }
}
