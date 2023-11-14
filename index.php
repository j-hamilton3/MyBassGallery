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

    // Get all the posts from the Post table.
    $query = "SELECT * FROM post ORDER BY date DESC;";

       // Make a prepare statement with the query.
    $statement = $db->prepare($query);
   
    // Execute the statement.
    $statement->execute();

    // Function to get a user's name by their corresponding ID.
    function getUserByID($userID, $db)
    {
        $userQuery = "SELECT * FROM users WHERE userID = " . $userID . ";";

        $userStatement = $db->prepare($userQuery);
   
        $userStatement->execute();

        return $userStatement->fetch();
    }
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
    <nav>
        <h1>Home</h1>
        <?php if(empty($_SESSION)) : ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <?php else : ?>
        <a href="create.php">Create a post</a>
        <a href="profile.php?userID=<?= $_SESSION['user']['userID'] ?>"><?= $_SESSION['user']['userName'] ?></a>
        <a href="login.php">Logout</a>
        <?php endif ?>   
    </nav>
    <?php while($post = $statement->fetch()) : ?>
        <div class="post">
            <h2><?= $post['title'] ?></h2>
            <p>Serial Number: <?= $post['serialNumber'] ?></p>
            <img src="<?= $post['image'] ?>" class="post-image">
            <p><?= html_entity_decode($post['content']) ?></p>
            <p>Post by user: <?= getUserByID($post['userID'], $db)['userName'] ?></p>
            <p>Created on: <?= $post['date'] ?></p>
        </div>
    <?php endwhile ?>
    
    
</body>
</html>