<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 14, 2023
    Description: A webpage for MyBassGallery that allows users to view all page categories.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Include the utility functions file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // Get list of categories from the categories table.
    $categories = "SELECT * FROM categories;";
    $categoriesStatement = $db->prepare($categories);

    $categoriesStatement->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Categories - MyBassGallery</title>
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
                <a href="register.php">Register</a> 
                <a href="login.php">Login</a>   
            </div>  
        <?php else : ?>
            <div class="links">
                <a href="create.php">Create a Post</a>
                <?php if(checkUserType() == 1) : ?>
                    <a href="adminManageUsers.php">Manage Users</a>
                    <a href="adminManageCategories.php">Manage Categories</a>
                <?php endif ?>
                <a class="username"><?= $_SESSION['user']['userName'] ?></a>
                <a href="login.php">Logout</a>
            </div>
        <?php endif ?>   
    </nav>
    <h1>Categories: </h1>
    <?php while($category = $categoriesStatement->fetch()) : ?>
        <a href="postsByCategory.php?id=<?= $category['categoryID'] ?>"><?= $category['categoryName'] ?> </a>
        <br>
        <br>
    <?php endwhile ?>
</body>
</html>