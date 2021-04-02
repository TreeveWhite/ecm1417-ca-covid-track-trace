<?php
    /**
     * Adds a visit (of a user who has reported being infected) to the Web Service.
     * 
     * @param string $x The x value of the visit.
     * @param string $y The y value of the visit.
     * @param string $date The date of the visit.
     * @param string $time The time of the visit.
     * @param string $duration The duration of the visit.
     */
    function addVisitToWebService(string $x, string $y, string $date, string $time, string $duration) {
        // Create an array of the values to send
        $visit = array("x" => $x, "y" => $y, "date" => $date, "time" => $time, "duration" => $duration);
        // Create a cURL session
        $curl = curl_init("http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/ctracker/report.php");
        $postString = http_build_query($visit, "", "&");
        // Set th options for the cURL
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // Excecute the cURL
        curl_exec($curl);
        // Close the cURL.
        curl_close($curl);
    }

    /**
     * Adds a new report to the MySQL database.
     * 
     * @param int $id The id of the current session (user).
     * @param string $date The date the user tested positive.
     * @param string $time The time the user tested positive.
     * 
     */
    function addReport(int $id, string $date, string $time) {
        // Create a connection to the MySQL database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Create a SQL statement to add the new report to the reports table in the data base
        $stmt = $conn -> prepare("INSERT INTO reports (userId, date, time)
        VALUES(?, ?, ?)");
        // Fill the statement with the given variables.
        $stmt -> bind_param("sss", $id, $date, $time);
        // Excecute the statement
        $stmt -> execute();
        // Close the statement
        $stmt -> close();
        // Close the connection to the database
        $conn -> close();
    }
    

    function getAllUserVisits(int $id) {
        // Creates a connection to the MySQL database.
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Creates a SQL statement to get the forename of the user with the given id
        $stmt = $conn -> prepare("SELECT date, duration, time, x, y from visits INNER JOIN locations ON visits.locationId = locations.id WHERE userId = ?");
        $stmt -> bind_param("s", $id);
        $stmt -> execute();
        // Gets the result of the statement
        $result = $stmt -> get_result();
        // Closes the connection
        $conn -> close();
        return $result;
    }

    // Resume the session
    session_start();

    // If the page recieves a POST request (ie from registerForm)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // get the date from the addReportForm
        $date = $_POST["date"];
        // get the time from the addReportForm
        $time = $_POST["time"];
        // Add a new reprt to the database
        addReport($_SESSION["id"], $date, $time);
        $userVisits = getAllUserVisits($_SESSION["id"]);
        while ($row = $userVisits -> fetch_assoc()) {
            addVisitToWebService($row["x"], $row["y"], $row["date"],$row["time"], $row["duration"]);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reportstylesheet.css">
    <title>COVID-CT: Visits Overview</title>
</head>
<body>
    <h1 class="topbar">COVID - 19 Contact Tracing</h1>
    
    <nav>
        <form action="/homepage.php" method="GET">
            <button type="submit" class="nav">Home</button>
        </form>
        <form action="/overviewpage.php" method="GET">
            <button type="submit" class="nav">Overview</button>
        </form>
        <form action="/addvisitpage.php" method="GET">
            <button type="submit" class="nav">Add Visit</button>
        </form>
        <form action="/reportpage.php" method="GET" style="background-color: rgb(132, 151, 176);">
            <button type="submit" class="nav">Report</button>
        </form>
        <form action="/settingspage.php" method="GET" >
            <button type="submit" class="nav">Settings</button>
        </form>
        <form action="/logout.php" method="GET">
            <button type="submit" class="nav final">Logout</button>
        </form>
    </nav>
    <section class="container">
        <img src="img/watermark.png" alt="watermark" class="watermark">
        <h2 class="subtitle">Report an Infection</h2>
        <hr>
        <p>Please report the date and time when you were tested positive for COVID - 19.</p>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" name="addReportForm" method="post">
            <input type="text" placeholder="Date" class="entry" name="date" id="date">
            <input type="text" placeholder="Time" class="entry" name="time" id="time"><br>
            <button type="submit" class="report" style="margin-right: 55%;">Report</button>
            <button type="reset" class="report">Cancel</button>
        </form>
    </section>
</body>
</html>