<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 25, 2023
    Description: A webpage for MyBassGallery that allows admins to manage users.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Include the utility functions file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // Get the user's userType.
    $userType = checkUserType();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Manage Users - MyBassGallery</title>
</head>
<body>
    <?php if($userType == 1) : ?>
        <h1> You are an admin and have permission to enter this page. </h1>
    <?php else : ?>
        <h1> You are not an admin and do not have permission.<a href="index.php">Home</a> </h1>
    <?php endif ?>
</body>
</html>