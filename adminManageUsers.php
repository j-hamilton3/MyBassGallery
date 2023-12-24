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

    // Query to get data of all non admin users.
    $users = "SELECT * FROM users WHERE userType != 1;";
    $usersStatement = $db->prepare($users);

    $usersStatement->execute();
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
    <nav class='navbar'>
        <a href="index.php">
            <div class="logo">
                <img src="uploads/mbg-logo.png" width="70px">
                <h1>MyBassGallery</h1>
            </div>
        </a>
        <?php if(empty($_SESSION)) : ?>
            <div class="links">
                <a href="categories.php">Categories</a>
                <a href="register.php">Register</a> 
                <a href="login.php">Login</a>
            </div>  
        <?php else : ?>
            <div class="links">
                <a href="create.php">Create a Post</a>
                <a href="categories.php">Categories</a>
                <?php if(checkUserType() == 1) : ?>
                    <a href="adminManageCategories.php">Manage Categories</a>
                <?php endif ?>
                <a href="profile.php?userID=<?= $_SESSION['user']['userID'] ?>"><?= $_SESSION['user']['userName'] ?></a>
                <a href="login.php">Logout</a>
            </div>
        <?php endif ?>   
    </nav>
    <?php if($userType == 1) : ?>
        <h1> Select a user to edit:</h1>
        <h2> List of users: </h2>
        <?php while($user = $usersStatement->fetch()) : ?>
            <li><a href="adminEditUsers.php?id=<?= $user['userID'] ?>">Username: <?= $user['userName'] ?> | User Type: <?= checkUserTypeNumber($user['userType']) ?></a></li>
        <?php endwhile ?>
    <?php else : ?>
        <p class="error"> You do not have permission to use this page. Return to <a href="index.php"> Home.</a></p>
    <?php endif ?>
</body>
</html>