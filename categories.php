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
    <h1>Categories: </h1>
    <?php while($category = $categoriesStatement->fetch()) : ?>
        <a href="postsByCategory.php?id=<?= $category['categoryID'] ?>"><?= $category['categoryName'] ?> </a>
        <br>
        <br>
    <?php endwhile ?>
</body>
</html>