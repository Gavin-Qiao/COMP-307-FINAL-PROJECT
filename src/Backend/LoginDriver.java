package Backend;
import static Backend.DatabaseStatement.*;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.sql.*;

public class LoginDriver extends DatabaseDriver
{
    /**
     *  LoginDriver class
     *  @param database path to the database
     * */
    public LoginDriver(String database)
    {
        super(database);
    }

    /**
     *  Use to check if a password is valid, i.e., the password
     *  <p>Contains ONLY [a-z0-9A-Z]</p>
     *  <p>Contains at lease:</p>
     *  <p>&nbsp;&nbsp;&nbsp;&nbsp;1 lowercase letter</p>
     *  <p>&nbsp;&nbsp;&nbsp;&nbsp;1 uppercase letter</p>
     *  <p>&nbsp;&nbsp;&nbsp;&nbsp;1 digit</p>
     *  <p>&nbsp;&nbsp;&nbsp;&nbsp;8 characters</p>
     *  <p>&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.w3schools.com/howto/tryit.asp?filename=tryhow_js_password_val">
     *     Javascript Link
     *     </a></p>
     * @param pw: input password
     * */
    public static void PasswordValidation(String pw) throws IllegalArgumentException
    {
        if (pw == null || pw.isBlank() || pw.isEmpty())
            throw new IllegalArgumentException("FATAL ERROR: Possible empty or null password!");

        if (pw.length() < 8 || pw.length() > 64)
            throw new IllegalArgumentException("ERROR: Incorrect password length: " + pw.length() + ", [8, 64] expected!");

        boolean contains_upper   = false;
        boolean contains_lower   = false;
        boolean contains_digit   = false;
        for(int i = 0; i < pw.length(); i++)
        {
            int c_ascii = pw.charAt(i);
            if (c_ascii > 47 && c_ascii < 58)
                contains_digit = true;
            else if(c_ascii > 64 && c_ascii < 91)
                contains_upper = true;
            else if(c_ascii > 96 && c_ascii < 123)
                contains_lower = true;
            else
                throw new IllegalArgumentException("ERROR: Password contains illegal character [" + (char)c_ascii + "]");
        }
        if (!(contains_digit && contains_lower && contains_upper)) throw new IllegalArgumentException("ERROR: Password does not meet requirements");
    }

    /**
     *  Using SHA-256 to encrypt password
     * @param pw: Input password
     * @return String of encrypted password by SHA-256 </br>
     * <p><a href="https://en.wikipedia.org/wiki/SHA-2"> SHA-2 introduction </a></p>
     * */
    public static String PasswordEncryption(String pw) throws NoSuchAlgorithmException, IllegalArgumentException
    {
        // Validate Password
        PasswordValidation(pw);

        // Get Hashcode
        MessageDigest digest = MessageDigest.getInstance("SHA-256");
        byte[] hash = digest.digest(pw.getBytes());

        // Convert to String
        StringBuilder outputString = new StringBuilder();
        for (byte b : hash)
        {
            outputString.append(Integer.toString((b & 0xff) + 0x100, 16).substring(1));
        }
        return outputString.toString();
    }

    /**
     *  Verify if given email exists in the database
     * @param email input email (assume valid)
     * @return
     * <p>true  <- email ALREADY  exists</p>
     * <p>false <- email DOES NOT exists</p>
     *
     * */
    public boolean user_email_existence(String email) throws SQLException
    {
        // Create connection to db
        Connection conn = super.createConnection();

        // Create statement
        PreparedStatement statement = conn.prepareStatement(FIND_USER_BY_EMAIL);
        statement.setString(1, email);

        // Execute Query
        ResultSet rs = statement.executeQuery();
        boolean existence = super.getResultSize(rs) > 0;

        // Close connection
        super.closeConnection(conn);
        return existence;
    }

    /**
     *  Verify if given username exists in the database
     * @param username input username (assume valid)
     * @return
     * <p>true  <- username ALREADY  exists</p>
     * <p>false <- username DOES NOT exists</p>
     *
     * */
    public boolean user_username_existence(String username) throws SQLException
    {
        // Create connection to db
        Connection conn = super.createConnection();

        // Create statement
        PreparedStatement statement = conn.prepareStatement(FIND_USER_BY_USERNAME);
        statement.setString(1, username);

        // Execute Query
        ResultSet rs = statement.executeQuery();
        boolean existence = super.getResultSize(rs) > 0;

        // Close connection
        super.closeConnection(conn);
        return existence;
    }

    /**
     * Verify if given userID exists in the database
     * @param userID input username (assume valid)
     * @return
     * <p>true  <- userID ALREADY  exists</p>
     * <p>false <- userID DOES NOT exists</p>
     *
     * */
    public boolean user_userID_existence(String userID) throws SQLException
    {
        // Create connection to db
        Connection conn = super.createConnection();

        // Create statement
        PreparedStatement statement = conn.prepareStatement(FIND_USER_BY_ID);
        statement.setString(1, userID);

        // Execute Query
        ResultSet rs = statement.executeQuery();
        boolean existence = super.getResultSize(rs) > 0;

        // Close connection
        super.closeConnection(conn);
        return existence;
    }

    /** TODO: Discuss tolerance for special character (e.g., é)
     * Reformat input name by putting the first letter uppercase and others lower case
     * @param name input name (might be first or last name)
     * @return
     * <p>Correct name format or</p>
     * <p>null <- if name is empty or null</p>
     * <p>""   <- if name contains non-alphabet character</p>
     * */
    private String name_reformat(String name)
    {
        if (name == null || name.isEmpty()) return null;

        StringBuilder formatted_name = new StringBuilder();
        for (int i = 0; i < name.length(); i++)
        {
            int c_ascii = name.charAt(i);

            // name contains char out of [a-z] or [A-Z]
            if(!((c_ascii > 64 && c_ascii < 91) || (c_ascii > 96 && c_ascii < 123)))
                return "";

            if (i == 0)     // First letter uppercase
                formatted_name.append(Character.toUpperCase(name.charAt(i)));
            else            // Other letter(s) lowercase
                formatted_name.append(Character.toLowerCase(name.charAt(i)));
        }
        return formatted_name.toString();
    }


    /** TODO: Testing
     *  Register a user to the system with full user information.
     * @param userName  read from user input: should be unique in db
     * @param pw        read from user input after validation (enter two times, length, lower/uppercase, etc.)
     * @param userID    read from user after verification of (userName, pw) pair: should be unique in db
     * @param firstName read from user after verification of (userName, pw) pair
     * @param lastName  read from user after verification of (userName, pw) pair
     * @param email     read from user after verification of (userName, pw) pair: should be unique in db
     * @return
     * <p>errCode: Status of registration</p>
     * <p> 0: Success<p/>
     * <p>-1: Incorrect username length ([1, 30] expected)</p>
     * <p>-2: Username already taken </p>
     * <p>-3: Incorrect password format</p>
     * <p>-4: Incorrect userID length: 9 expected!</p>
     * <p>-5: userID contains non-digit character</p>
     * <p>-6: userID already taken!</p>
     * <p>-7: Empty first/last name</p>
     * <p>-8: First/last name too long! (greater than 256/128 characters)</p>
     * <p>-9: First/last name contains illegal character</p>
     * <p>-10: Incorrect email length ([4, 320] expected)</p>
     * <p>-11: email already taken!</p>
     *
     * @see LoginDriver#PasswordValidation(String)
     * */
    public int registerUser(String userName, String pw, String userID, String firstName, String lastName, String email) throws SQLException
    {
        // Verify userName
        if (userName.isEmpty() || userName.length() > 30)  return -1; // Incorrect username length ([1, 30] expected)
        if (user_username_existence(userName))             return -2; // Username already taken

        // Verify password
        try
        {
            PasswordValidation(pw);
        }
        catch (IllegalArgumentException e)
        {
            return -3; // Incorrect password format
        }

        // Verify userID TODO:Design choice
        if (userID.length() != 9) return -4; // Incorrect userID length: 9 expected!
        for (int i = 0; i < userID.length(); i++)
        {
            int c_ascii = userID.charAt(i);
            if (c_ascii < 48 || c_ascii > 57)
                return -5; // userID contains non-digit character
        }
        if (user_userID_existence(userID))
            return -6;     // userID already taken!

        // Verify first/last name
        firstName = name_reformat(firstName);
        lastName  = name_reformat(lastName);
        if (firstName == null || lastName == null)
            return -7; // Empty first/last name
        if (firstName.length() > 256 || lastName.length() > 128)
            return -8; // First/last name too long! (greater than 256/128 characters)
        if (firstName.isEmpty() || lastName.isEmpty())
            return -9; // First/last name contains illegal character

        // Verify email
        if (email == null || email.length() < 3 || email.length() > 320)
            return -10; // Incorrect email length ([4, 320] expected)
        if (user_email_existence(email))
            return -11; // email already taken!

        // Create connection:
        Connection conn = super.createConnection();

        // Create statement
        PreparedStatement statement = conn.prepareStatement(REGISTER_USER);
        statement.setString(1, userName );
        statement.setString(2, pw       );
        statement.setString(3, userID   );
        statement.setString(4, firstName);
        statement.setString(5, lastName );
        statement.setString(6, email    );

        // Execute Insertion
        statement.executeUpdate();

        // Close connection
        super.closeConnection(conn);

        return 0;
    }
}
