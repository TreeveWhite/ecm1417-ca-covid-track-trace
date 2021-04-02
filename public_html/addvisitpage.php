<?php
    /**
     * Adds a new visit to the database using binding variables to protect from SQL injection.
     * 
     * @param string $userId This is the id of the client ading the visit.
     * @param string $locaationId This is the id of the location the visit is taking place at.
     * @param string $date This is the date of the new visit.
     * @param string $time This is the time of the new visit.
     * @param string $duration This is the duration of the new visit.
     */
    function addVisitToDb(string $userId, string $locaationId, string $date, string $time, string $duration) {
        // Create a connection to the MySQL database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Create a SQL statement to add a new visit
        $stmt = $conn -> prepare("INSERT INTO visits (userID, locationId, date, time, duration)
        VALUES(?, ?, ?, ?, ?)");
        // Fill the statement with the given variables
        $stmt -> bind_param("sssss", $userId, $locaationId, $date, $time, $duration);
        // Excecute the statement
        $stmt -> execute();
        // Close the statement
        $stmt -> close();
        // Close the connection to the database
        $conn -> close();
    }
    
    /**
     * Adds a new location to the database.
     * 
     * @param string $x This is the x co-ordinant of the visit's location.
     * @param string $y This is the y co-ordinant of the visit's location.
     */
    function addLocationToDb(string $x, string $y) {
        // Create a connection to the MySQL database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Create a SQL statement to add a new location
        $stmt = $conn -> prepare("INSERT INTO locations (x, y)
        VALUES(?, ?)");
        // Fill the statement with the given x and y values
        $stmt -> bind_param("ii", $x, $y);
        // Excecute the statement
        $stmt -> execute();
        // Get the id of the new location row
        $locationId = $conn->insert_id;
        // Close the statement
        $stmt -> close();
        // Close the connection to the database
        $conn -> close();
        // Return the new location id
        return $locationId;
    }

    // Resume the session
    session_start();

    // If the server recieves a POST request (ie from loginForm)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get username from POST request header
        $date = $_POST["date"];
        // Get password from POST request header
        $time = $_POST["time"];
        // Get password from POST request header
        $duration = $_POST["duration"];
        // Get password from POST request header
        $x = $_POST["xCoordinant"];
        // Get password from POST request header
        $y = $_POST["yCoordinant"];
        // Atempt to login user with given credentials
        $locationId = addLocationToDb($x, $y);
        addVisitToDb($_SESSION["id"], $locationId, $date, $time, $duration);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/addvisitstylesheet.css?v=2">
    <script src="js/mapFunctions.js"></script>
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
        <form action="/addvisitpage.php" method="GET" style="background-color: rgb(132, 151, 176);">
            <button type="submit" class="nav">Add Visit</button>
        </form>
        <form action="/reportpage.php" method="GET">
            <button type="submit" class="nav">Report</button>
        </form>
        <form action="/settingspage.php" method="GET">
            <button type="submit" class="nav">Settings</button>
        </form>
        <form action="/logout.php" method="GET">
            <button type="submit" class="nav final">Logout</button>
        </form>
    </nav>
    <section class="container">
        <img src="img/watermark.png" alt="watermark" class="watermark">
        <h2 class="subtitle">Add a new Visit</h2>
        <hr>
        <div class="left-half">
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="addVisit">
                <input type="text" name="date" placeholder="Date" class="entry"><br>
                <input type="text" name="time" placeholder="Time" class="entry"><br>
                <input type="text" name="duration" placeholder="Duration" class="entry"><br>
                <input type="text" hidden id="xCoordinant" name="xCoordinant">
                <input type="text" hidden id="yCoordinant" name="yCoordinant">
                <button type="submit" class="add bottom2">Add</button><br>
                <button type="reset" class="add bottom1">Cancel</button>
            </form>
        </div>
        <div class="right-half">
            <img src="img/marker_black.png" alt="marker" class="marker" id="markerPoint">
            <img src="img/exeter.jpg" alt="exeter" class="map" onclick="getCoordinants(event, 'xCoordinant', 'yCoordinant');">
        </div>
    </section>
</body>
</html>