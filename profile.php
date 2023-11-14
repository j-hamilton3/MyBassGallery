<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 4, 2023
    Description: A webpage for MyBassGallery that displays a user's profile.

****************/
    
    // There must be a DB connection to continue.
    require('connect.php'); 

    // Start/Resume the session.
    session_start();

    if (isset($_GET['userID'])) {
        
        // Sanitize the id from the input GET.
        $id = filter_input(INPUT_GET, 'userID', FILTER_SANITIZE_NUMBER_INT);

        // Build the SQL query using the filtered id.
        $query = "SELECT * FROM users WHERE userID = :userID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $id, PDO::PARAM_INT);

        // Execute the SELECT and get the single row.
        $statement->execute();
        $user = $statement->fetch();

    // If the $id is false.
    } else {
        $id = false;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>{Profile} - MyBassGallery</title>
</head>
<body>
    <?php if($id) : ?>
    <h1><?= $user['userName'] ?></h1>
    <?php else : ?>
    <p class="error">An error has occured, please return to the home page.</p>
    <a href="index.php">Home</a>
    <?php endif ?>
</body>
</html>