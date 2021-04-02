<?php
    /**
     * Attempts to login a client using given username and password. If the login is 
     * successful the the session's id is set to be the same as the account id which
     * they have entered the username and password for.
     * 
     * @param string $username This is the entered username.
     * @param string $password This is the entered password.
     * 
     * @return bool Was the user able to be logged in.
     */
    function loginUser(string $username, string $password) {
        // Creates a connection to the database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Creates a SQL statement to get all user credentials from the database
        $sql = "SELECT id, username, password FROM users";
        // Gets all the user credentials from output of statement
        $result = $conn->query($sql);

        // Loops through all the credentials
        while($row = $result->fetch_assoc()) {
            // Checks if the enetred username and password have a match in the database
            if(($row["username"] == $username) and (password_verify($password, $row["password"]))) {
                // If there is a match
                // Asigns the account id to the session id variable
                $_SESSION["id"] = $row["id"];
                // Assigns the num weeks to consider a connection to a defult of 2
                setcookie("window", 2);
                // Asigns the distance to consider a connection to defult 50
                setcookie("distance", 50);
                // Relocates the user to the homepage
                header("Location: homepage.php");
                // Closes the connection
                $conn -> close();
                return true;
            }
        }
        // There was no match so unable to login
        // Close the connection
        $conn -> close();
        return false;
    }
    
    // Starts or resumes the session
    session_start();
    // If id ready exists then send to homepage with that id
    if (isset($_SESSION["id"])) {
        header("Location: homepage.php");
    }
    // If the server recieves a POST request (ie from loginForm)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get username from POST request header
        $username = $_POST["username"];
        // Get password from POST request header
        $password = $_POST["password"];
        // Atempt to login user with given credentials
        loginUser($username, $password);
    }
    
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/authenticationstylesheet.css">
    <title>COVID-CT: Login</title>
</head>
<body>
    <h1 class="topbar">COVID - 19 Contact Tracing</h1>
    <img src="img/watermark.png" alt="watermark" class="watermark">
    <br><br><br>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <input type="text" name="username" id="username" placeholder="Username" class="entry">
        <br>
        <input type="password" name="password" id="password" placeholder="Password" class="entry">
        <br><br><br>
        <button type="submit" class="login">Login</button>
        <button type="reset" class="login">Cancel</button>
    </form>
    <br>
    <form action="/registrationpage.php" method="get">
        <button type="submit" class="register">Register</button>
    </form>
</body>
</html>