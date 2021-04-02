<?php
    /**
     * Delete a specific visit from the visits table in the data base.
     * 
     * @param string $id The id of the user which the visit relates to.
     * @param string $date The date of the visit to be deleted.
     * @param string $time The time of the visit to be deleted.
     * @param string $duration The duration of the visit to be deleted.
     * @param int $x The x co-ordinant of the visit to be deleted.
     * @param int $y The y co-ordinant of the visit to be deleted.
     */
    function deleteVisit(string $id, string $date, string $time, string $duration, int $x, int $y) {
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        $stmt1 = $conn -> prepare("DELETE v FROM visits v INNER JOIN locations l ON v.locationId = l.id WHERE userId = ? AND `date`= ? AND duration= ? AND `time`= ? AND x= ? AND y= ?");
        $stmt1 -> bind_param("ssssii", $id, $date, $duration, $time, $x, $y);
        $stmt1 -> execute();
        $stmt1 -> close();
    }

    /**
     * Delete any loctations which are not foriegn keys to a visit in the visits table in
     * the data base.
     */
    function deleteUnusedLocations() {
        $conn = new mysqli("localhost", "root", "password", "ecm1417-ca1");
        $stmt2 = "DELETE FROM locations WHERE id NOT IN (SELECT locationId FROM visits)";
        $conn -> query($stmt2) or die($conn -> error);
        $conn -> close();
    }

    // Resume the session
    session_start();
    // Get the needed data from the GET request and current session
    $id = $_SESSION["id"];
    $date = $_GET["date"];
    $time = $_GET["time"];
    $duration = $_GET["duration"];
    $x = $_GET["x"];
    $y = $_GET["y"];
    echo $id." ".$date." ".$time." ".$duration." ".$x." ".$y;
    // Delete the visit with the given parameters
    deleteVisit($id, $date, $time, $duration, $x, $y);
    // Delete any unused locations
    deleteUnusedLocations();
?>