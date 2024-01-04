<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 26, 2023
    Description: A webpage for MyBassGallery that allows admins to manage Categories.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Include the utility functions file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // Get the user's userType.
    $userType = checkUserType();

    // Create the error flag.
    $categoryFlag = false;
    $categoryTakenFlag = false;

    // If the create new category form is posted.
    if($_POST)
    {
        // If the category name is 0 or more than 30 characters.
        if(!$_POST['categoryName'] || strlen($_POST['categoryName']) > 30)
        {
            $categoryFlag = true;
        }
        else
        {
            $categoryName = $_POST['categoryName'];
            
            // Create the SQL Query -> Checks if the matching category in the DB.
            $query = "SELECT * FROM Categories WHERE categoryName = :categoryName";
            $statement = $db->prepare($query);

            // Bind the username value.
            $statement->bindValue(":categoryName", $categoryName);

            // Execute the statement.
            $statement->execute();

            // Check if there is a username.
            $categoryInDB = $statement->fetch(); 

            if ($categoryInDB)
            {
                $categoryTakenFlag = true;
            }
        }

        // If the flags aren't set, we can persist the new category to the DB.
        if(!$categoryFlag && !$categoryTakenFlag)
        {
            // Sanitize the categoryName input.
            $categoryName = filter_input(INPUT_POST, 'categoryName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Create the query for the insert.
            $query = "INSERT INTO Categories (categoryName) VALUES (:categoryName)";
            $statement = $db->prepare($query);
  
            // Bind the values.
            $statement->bindValue(":categoryName", $categoryName);
  
            // Execute the INSERT.
            $statement->execute();
        }
    }

    // Query to get data of all non admin users.
    $categories = "SELECT * FROM categories";
    $categoriesStatement = $db->prepare($categories);

    $categoriesStatement->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Manage Categories - MyBassGallery</title>
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
                    <a href="adminManageUsers.php">Manage Users</a>
                <?php endif ?>
                <a class="username"><?= $_SESSION['user']['userName'] ?></a>
                <a href="login.php">Logout</a>
            </div>
        <?php endif ?>   
    </nav>
    <?php if($userType == 1) : ?>
        <h1> Select a category to edit:</h1>
        <h2> List of categories: </h2>
        <?php while($category = $categoriesStatement->fetch()) : ?>
            <li><a href="adminEditCategories.php?id=<?= $category['categoryID'] ?>"><?= $category['categoryName'] ?></a></li>
        <?php endwhile ?>
        <h2> Create a new category: </h2>
        <form method="post">
            <label for="categoryName"> Category Name: </label>
            <input name="categoryName" id="categoryNameInput">
            <input type="submit" name="submit" value="Create Category">
            <?php if($categoryFlag) : ?>
                <p class="error">Please enter a category name. (Maximum 30 characters)</p>
            <?php endif ?>
            <?php if($categoryTakenFlag) : ?>
                <p class="error">Category name already exists.</p>
            <?php endif ?>
        </form>
    <?php else : ?>
        <p class="error"> You do not have permission to use this page. Return to <a href="index.php"> Home.</a></p>
    <?php endif ?>
</body>
</html>