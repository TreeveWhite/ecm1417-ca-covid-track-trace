<?php
    /**
     * Gets all the visits for the current user id.
     * 
     * @param int $id The session id (the logged in client's id).
     * 
     * @return object $result An object which contains the rows of the result of the sql statement.
     */
    function getVisits(int $id) {
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

    // resume the session
    session_start();
    // get all the users visits
    $visits = getVisits($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/overviewstylesheet.css">
    <script src="js/visitFunctions.js"></script>
    <title>COVID-CT: Visits Overview</title>
</head>
<body>
    <h1 class="topbar">COVID - 19 Contact Tracing</h1>
    
    <nav>
        <form action="/homepage.php" method="GET">
            <button type="submit" class="nav">Home</button>
        </form>
        <form action="/overviewpage.php" method="GET" style="background-color: rgb(132, 151, 176);">
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
    <article>
        <img src="img/watermark.png" alt="watermark" class="watermark">
        <table class="overview" id="overviewTable">
            <tr>
                <th class="date">Date</th>
                <th class="time">Time</th>
                <th class="data">Duration</th>
                <th class="data">X</th>
                <th class="data">Y</th>
                <th class="data"></th>
            </tr>
            <?php
                $rowId = 1;
                // loop over every row of the user's visits
                while ($row = $visits -> fetch_assoc()) {
                    $date = htmlspecialchars($row["date"]);
                    $time = htmlspecialchars($row["time"]);
                    $duration = htmlspecialchars($row["duration"]);
                    $x = htmlspecialchars($row["x"]);
                    $y = htmlspecialchars($row["y"]);
                    echo "<tr>";
                    // display the date of the visit
                    echo "<td class='date'>$date</td>";
                    // display the time of the visit
                    echo "<td class='time'>$time</td>";
                    // display the duration of the visit
                    echo "<td class='data'>$duration</td>";
                    // display the x co-ordinant of the visit
                    echo "<td class='data'>$x</td>";
                    // display the y co-ordinant of the visit
                    echo "<td class='data'>$y</td>";
                    // dipslay an exit button
                    echo '<td class="data"><img src="img/cross.png" alt="cross" class="cross" onclick="deleteVisit('.$rowId.', \''.$date.'\', \''.$time.'\', \''.$duration.'\', '.$x.', '.$y.');"></td>';
                    echo "</tr>";
                    $rowId++;
                }
            ?>
        </table>
    </article>
</body>
</html>