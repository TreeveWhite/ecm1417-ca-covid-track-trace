<?php
    /**
     * Update the widnow and distance cookies.
     * 
     * @param int $window The time window in weeks the user wants to consider for a possible connection.
     * @param int $distance The distance between visists to consider (range 0 -> 500).
     */
    function updateSettings(int $window, int $distance) {
        // Set the window and distance cookie to match the given parameters
        setcookie("window", $window);
        setcookie("distance", $distance);
    }

    // Resumes the session
    session_start();
    // If the platform recieves a POST request from the user
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the data from the settings form which made the request
        $window = $_POST["window"];
        $distance = $_POST["distance"];
        // Update the cookies to now reflect these newly entered values
        updateSettings($window, $distance);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/settingsstylesheet.css">
    <title>COVID-CT: Settings</title>
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
        <form action="/reportpage.php" method="GET">
            <button type="submit" class="nav">Report</button>
        </form>
        <form action="/settingspage.php" method="GET" style="background-color: rgb(132, 151, 176);">
            <button type="submit" class="nav">Settings</button>
        </form>
        <form action="/logout.php" method="GET">
            <button type="submit" class="nav final">Logout</button>
        </form>
    </nav>
    <section class="container">
        <img src="img/watermark.png" alt="watermark" class="watermark">
        <h2 class="subtitle">Alert Settings</h2>
        <hr>
        <p>Here you may change the alert distance and the time span for which the contact tracing will be performed.</p>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
            <label for="window">window<select name="window" id="window" class="drop-menu">
                <option value="" selected="selected" disabled></option>
                <option value=1>One Week</option>
                <option value=2>Two Weeks</option>
                <option value=3>Three Weeks</option>
                <option value=4>Four Weeks</option>
                </select>
            </label><br>
            <label for="distance">distance<input type="number" min="0" max="500" id="distance" name="distance" class="entry"></label>
            <button type="submit" class="report" style="margin-right: 55%;">Report</button>
            <button type="reset" class="report">Cancel</button>
        </form>
    </section>
</body>
</html>