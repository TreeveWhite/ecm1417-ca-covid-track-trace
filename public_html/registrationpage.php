<?php
    /**
     * Creates a Javascript alert with the given message.
     * 
     * @param string $msg The message to be displayed in the alert box.
     */
    function send_alert(string $msg) {
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }

    /**
     * Checks if a password is a valid by only containing lower case, upper case or digits and
     * is longer than 7 characters (>=8).
     * 
     * @param $password The password to be checked.
     * 
     * @return bool If the password is valid.
     */
    function checkValidPassword(string $password) {
        $arrayPass = str_split($password);
        // Loops through each character in the password
        foreach ($arrayPass as $char) {
            // Check if the character is not a lowercase, uppercase or digit
            if (!((ctype_lower($char)) || (ctype_upper($char)) || (ctype_digit($char)))) {
                return false;
            }
        }
        // Check the length of the password is atleast 8
        if (strlen($password) < 8) {
            return false;
        }
        else {
            return true;
        }
        
    }

    /**
     * Creates a new user (account) in the MySQL database.
     * 
     * @param string $username This is the username for the new account.
     * @param string $password This is te password for the new account.
     * @param string $forename This is the forename of the new account's user.
     * @param string $surname This is the surname of the new aaccount's user.
     * 
     * @return int $newUserId This is the id of the account which has been logged into.
     * (-1 means a failed login).
     */
    function create_account(string $username, string $password, string $forename, string $surname) {
        // Create a connection to the MySQL database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Check the given password is valid
        if (!checkValidPassword($password)){
            send_alert("Password is too short or conatins characters other than upper case, lowercase and numbers. Must be at least 8 characters and only contain the correct types of character.");
        }
        else {
            // Create a SQL statement to add the new account to the database.
            $stmt = $conn -> prepare("INSERT INTO users (username, password, forename, surname)
            VALUES(?, ?, ?, ?)");
            $password = password_hash($password, PASSWORD_BCRYPT);
            // Fill the statement with the given variables.
            $stmt -> bind_param("ssss", $username, $password, $forename, $surname);
            // Excecute the statement
            $stmt -> execute();
            // Close the statement
            $stmt -> close();
            $newUserId = $conn->insert_id;
            // Close the connection to the database
            $conn -> close();
            return $newUserId;
        }
        // unable to create the account
        
        // Close the connection to the database
        $conn -> close();
        return -1;
    }

    // If the page recieves a POST request (ie from registerForm)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the username from the POST request header
        $username = $_POST["username"];
        // Get the password from the POST request header
        $password = $_POST["password"];
        // Get the forename from the POST request header
        $forename = $_POST["forename"];
        // Get the surname from the POST request header
        $surname = $_POST["surname"];

        // Create a new user
        $result = create_account($username, $password, $forename, $surname);

        // Check if creating new user was successful (-1 means not successful)
        if ($result != -1) {
            // Relocate the client to the login page.
            session_start();
            $_SESSION["id"] = $result;
            // Assigns the num weeks to consider a connection to a defult of 2
            setcookie("window", 2);
            // Asigns the distance to consider a connection to defult 50
            setcookie("distance", 50);
            header("Location: index.php");
        }
        else {
            // Send alert that creating new user was unsuccessful
            send_alert("Unable to create account.");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/authenticationstylesheet.css">
    <script src="js/registerPage.js"></script>
    <title>COVID-CT: Registration</title>
</head>
<body>
    <h1 class="topbar">COVID - 19 Contact Tracing</h1>
    <img src="img/watermark.png" alt="watermark" class="watermark">
    <br><br><br>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="registerForm" onsubmit="return validateRegisterForm()">
        <input type="text" name="forename" id="forename" placeholder="Name" class="entry">
        <br>
        <input type="text" name="surname" id="surname" placeholder="Surname" class="entry">
        <br>
        <input type="text" name="username" id="username" placeholder="Username" class="entry">
        <br>
        <input type="password" name="password" id="password" placeholder="Password" class="entry">
        <br><br><br>
        <button type="submit" class="register">Register</button>
    </form>
</body>
</html>