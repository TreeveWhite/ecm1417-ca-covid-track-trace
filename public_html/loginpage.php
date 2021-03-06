<?php
    $servername = "localhost";
    $username = "root";
    $password = "";

    $conn = new mysqli_connect($servername, $username, $password);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/authenticationstylesheet.css">
    <title>COVID-19 Contact Tracing</title>
</head>
<body>
    <h1 class="topbar">COVID - 19 Contact Tracing</h1>
    <img src="img/watermark.png" alt="watermark" class="watermark">
    <br><br><br>
    <form action="/login" method="post">
        <input type="text" name="username" id="username" placeholder="Username" class="entry">
        <br>
        <input type="password" name="password" id="password" placeholder="Password" class="entry">
        <br><br><br>
        <button type="submit" class="login">Login</button>
        <button type="reset" class="login">Cancel</button>
    </form>
    <br>
    <form action="/register" method="get">
        <button type="submit" class="register">Register</button>
    </form>
</body>
</html>