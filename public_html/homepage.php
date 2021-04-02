<?php
    /**
     * Gets the visits of those who has reported COVID infections from the web service.
     * 
     * @param int $window The number of weeks to look back at for connections between visits.
     * 
     * @return array $infectedVisits An array of the visit which were returned by the web service.
     */
    function getInfectedFromWebSevice(int $window) {
        $infectedVisits = array();
        $json = file_get_contents("http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/ctracker/infections.php?ts=".$window);
        $data = json_decode($json, TRUE);
        foreach ($data as $row) {
            $infectedVisits[] = array_merge($row, array("reportDate" => "Unknown", "reportTime" => "Unknown"));
        }
        return $infectedVisits;
    }

    /**
     * Gets the forename of the account with the given unique id.
     * 
     * @param int $id This is the unqiue id which belongs to the current session.
     * 
     * @return string $name The name of the user (the name which relates to the id of the session).
     */
    function getName(int $id) {
        // Creates a connection to the MySQL database.
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Creates a SQL statement to get the forename of the user with the given id
        $stmt = $conn -> prepare("SELECT forename from users WHERE id = ?");
        $stmt -> bind_param("s", $id);
        $stmt -> execute();
        // Gets the result of the statement
        $result = $stmt -> get_result();
        while(($row = $result->fetch_assoc()) !== null) {
            $name = $row["forename"];
        }
        // Closes the connection
        $conn -> close();
        // Return the forename of the account
        return $name;
    }

    /**
     * Checks if a date and time is within a certain number of weeks prior to the 
     * current date.
     * 
     * @param string $date The date to check if in the last n weeks.
     * @param string $time The specific time of the date to check.
     * @param int $numWeeks The number of weeks to include in the range.
     * 
     * @return bool If the date falls within the specified range.
     */
    function isWithinWeeks(string $date, string $time, int $numWeeks){
        // Create a specific time for the start date and end date of the range
        $windowStart = strtotime("-$numWeeks week", strtotime("now"));
        $windowEnd = strtotime("now");
        // Create a specific time for the visit date
        $visitDate = strtotime(strtr($date, "/", "-")." ".$time);
        // Check if the visit date lies between the range
        if (($visitDate >= $windowStart) && ($visitDate <= $windowEnd)){
            // Return true if it does
            return true;
        }
        else {
            // Return false if it doesnt
            return false;
        }
    }

    /**
     * Check if two visits (a start and end) overlap at all, using their dates, times and durations
     * 
     * @param string $startDate The date of the start (visit 1).
     * @param string $startTime The time of the start (visit 1).
     * @param string $startDuration The duration of the start (visit 1).
     * @param string $endDate The date of the end (visit 2).
     * @param string $endTime The time of the end (visit 2).
     * @param string $endDuration The duration of the end (visit 2).
     * 
     * @return bool If the two events overlap at all.
     */
    function isOverlap(string $startDate, string $startTime, string $startDuration, string $endDate, string $endTime, string $endDuration) {
        // Create a specific time for the visits related to a reported infection
        $reportVisitStart = strtotime(strtr($endDate, "/", "-")." ".$endTime);
        $reportVisitEnd = strtotime("+$endDuration minutes", strtotime(strtr($endDate, "/", "-")." ".$endTime));
        // Create a specific time for the user's visit
        $visitStart = strtotime(strtr($startDate, "/", "-")." ".$startTime);
        $visitEnd = strtotime("+$startDuration minutes", strtotime(strtr($startDate, "/", "-")." ".$startTime));
        // Check if there is any overlap between the ranges of times.
        if (($visitStart <= $reportVisitEnd) && ($visitEnd >= $reportVisitStart)){
            // Return true if there is an overlap
            return true;
        }
        else {
            // Return false is there isnt an overlap
            return false;
        }
    }

    /**
     * Gets all the visits for the current user id.
     * 
     * @param int $id The session id (the logged in client's id).
     * @param int $window The num weeks prior of visits to display.
     * 
     * @return array $validCoords The visits which fall within the window of weeks
     * the user wants to check for connections. 
     */
    function getClientVisits(int $id, int $window) {
        // Creates a connection to the MySQL database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Creates a SQL statement to get the user's visits from the visits table
        $stmt = $conn -> prepare("SELECT x, y, visits.date, visits.time, visits.duration from visits INNER JOIN locations ON visits.locationId = locations.id WHERE userId = ?");
        $stmt -> bind_param("s", $id);
        $stmt -> execute();
        // Gets the result of the statement
        $result = $stmt -> get_result();
        $validCoords = array();
        // Loop over the results of the SQL query
        while ($row = $result->fetch_assoc()){
            // Check if the visit is within range of weeks the user has specified to check for connections
            if (isWithinWeeks($row["date"], $row["time"], $window)) {
                $validCoords[] = $row;
            }
        }
        // Closes the connection
        $conn -> close();
        // Return the visits which are valid
        return $validCoords;
    }

    /**
     * Gets all the visits of users who have reported a COVID infection.
     * 
     * @param int $window The number of weeks which to look back to check for visits.
     * 
     * @return array $allVisits All the visits of users who have reported an infection.
     */
    function getInfectedVisits(int $window) {
        // Creates a connection to the MySQL database
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        // Creates a SQL statement to get the locations of the user's visits
        $stmt = "SELECT
            x,
            y,
            visits.date,
            visits.time,
            visits.duration,
            reports.date AS reportDate,
            reports.time AS reportTime
        FROM
            visits
        INNER JOIN locations ON visits.locationId = locations.id
        INNER JOIN reports ON reports.userId = visits.userId
        WHERE
            visits.userId IN(
        SELECT
            userId
        FROM
            reports
        )";
        // Gets the result of the statement
        $result = $conn->query($stmt) or die($conn->error);
        $validCoords = array();
        while ($row = $result->fetch_assoc()){
            if (isWithinWeeks($row["date"], $row["time"], $window)) {
                $validCoords[] = $row;
            }
        }
        // Closes the connection
        $conn -> close();
        $webServiceInfections = getInfectedFromWebSevice($window);
        $allVisits = array_merge($validCoords, $webServiceInfections);
        return $allVisits;
    }

    /**
     * Calculates the Euclidean Distance between two co-ordinates.
     * 
     * @param int $x1 The x value of the first co-ordinant.
     * @param int $y1 The y value of the first co-ordinant.
     * @param int $x2 The x value of the second co-ordinant.
     * @param int $y2 The y value of the second co-ordinant. 
     * 
     * @return int The Euclidean Distance between the two co-ordinants.
     */
    function calculateEucliean(int $x1, int $y1, int $x2, int $y2){
        return sqrt(($x2 - $x1)**2 + ($y2 - $y1)**2);
    }

    /**
     * Gets the visits where the user has been within their defined distance to a visit of an infected
     * user's visit within the same time. 
     * 
     * @param $clientVisit An array of the clients visits.
     * @param $infectedVisits An array of all the visits which are related to users who has reported a 
     * COVID infection.
     * @param $distance The max distance between visits to consider a connection.
     * 
     * @return array $validCoords The visits which need to be displayed in red as they are where the user 
     * has been in contact with a reported infected user. 
     */
    function getRedVisits(array $clientVisits, array $infectedVisits, int $distance) {
        $validCoords = array();
        // Loop over each of the users visits
        foreach ($clientVisits as $clientVisit) {
            // Loop over each of the infected user's visits
            foreach ($infectedVisits as $infectedVisit) {
                // Check if the distance between the client's visit and the infected user's visit is less than distance
                if (calculateEucliean($clientVisit["x"], $clientVisit["y"], $infectedVisit["x"], $infectedVisit["y"]) <= $distance) {
                    // Check if the client's visit and the infected user's visit overlap in time
                    if (isOverlap($clientVisit["date"], $clientVisit["time"], $clientVisit["duration"], $infectedVisit["date"], $infectedVisit["time"], $infectedVisit["duration"])){
                        // Add the infected user's visit to the array of visits to display as red
                        $validCoords[] = $infectedVisit;
                    }
                }
            }
        }
        // Return the visits to be displayed as red
        return $validCoords;
    }
    
    // Resume the session
    session_start();
    // Get the id from the Session
    $id = $_SESSION["id"];
    // Get the forename of the account
    $name = getName($id);
    // Get the num of weeks to consider a connection
    $window = $_COOKIE["window"];
    // Get the distance to consider between visits
    $distance = $_COOKIE["distance"];
    // Get the current loged in users visits
    $clientVisits = getClientVisits($id, $window);
    // Get the visits of the user
    $allInfectedVisits = getInfectedVisits($window);
    // Get the red warning visits
    $contactVisits = getRedVisits($clientVisits, $allInfectedVisits, $distance);
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/homestylesheet.css">
    <script src="js/mapFunctions.js"></script>
    <title>COVID-CT: Home Page</title>
</head>
<body>
    <h1 class="topbar">COVID - 19 Contact Tracing</h1>
    
    <nav>
        <form action="/homepage.php" method="GET">
            <button type="submit" class="nav" style="background-color: rgb(132, 151, 176);">Home</button>
        </form>
        <form action="/overviewpage.php" method="GET">
            <button type="submit" class="nav">Overview</button>
        </form>
        <form action="/addvisitpage.php" method="GET">
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
        <h2 class="subtitle">Status</h2>
        <hr>
        <div class="left-half">
            <p>Hi <?= $name ?>, you might have had a connection to an infected person at the location shown in red.</p>
            <p class="bottom">Click on the marker to see details about the infection.</p>
        </div>
        <div class="right-half">
            <?php
            foreach ($allInfectedVisits as $coordinant) {
                if (!in_array($coordinant, $contactVisits)) {
                    $x = htmlspecialchars($coordinant["x"]);
                    $y = htmlspecialchars($coordinant["y"]);
                    $date = htmlspecialchars($coordinant["date"]);
                    $time = htmlspecialchars($coordinant["time"]);
                    $duration = htmlspecialchars($coordinant["duration"]);
                    $reportDate = htmlspecialchars($coordinant["reportDate"]);
                    $reportTime = htmlspecialchars($coordinant["reportTime"]);
                    echo '<img src="img/marker_black.png" alt="marker" class="marker" style="top:'.$y.'px;left:'.$x.'px;" onclick="displayInfectionDetails(\''.$reportDate.'\',\''.$reportTime.'\',\''.$date.'\',\''.$time.'\',\''.$duration.'\')">';
                }
            }
            foreach ($contactVisits as $coordinant) {
                $x = htmlspecialchars($coordinant["x"]);
                $y = htmlspecialchars($coordinant["y"]);
                $date = htmlspecialchars($coordinant["date"]);
                $time = htmlspecialchars($coordinant["time"]);
                $duration = htmlspecialchars($coordinant["duration"]);
                $reportDate = htmlspecialchars($coordinant["reportDate"]);
                $reportTime = htmlspecialchars($coordinant["reportTime"]);
                echo '<img src="img/marker_red.png" alt="marker" class="marker" style="top:'.$y.'px;left:'.$x.'px;" onclick="displayInfectionDetails(\''.$reportDate.'\',\''.$reportTime.'\',\''.$date.'\',\''.$time.'\',\''.$duration.'\')">';
            }
            ?> 
            <img src="img/exeter.jpg" alt="exeter" class="map">
        </div>
    </section>
        
</body>
</html>