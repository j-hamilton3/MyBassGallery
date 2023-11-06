<?php

/*******w******** 

    Name: James Hamilton
    Date: November 4, 2023
    Description: The main page for MyBassGallery - it displays user created posts in a feed.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Start/Resume the session.
    session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Home - MyBassGallery</title>
</head>
<body>
    <h1>Home</h1>
    <a href="login.php">Login</a>
</body>
</html>