<?php
    // Resumes the session
    session_start();
    // Deletes the current session 
    session_destroy();
    // Sends the client back to the login page
    header("Location: index.php");
?>