<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 26, 2023
    Description: A webpage for MyBassGallery that allows admins to edit categories.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Include the utility functions file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // Get the user's userType.
    $userType = checkUserType();

    // Check if GET is set and query the DB.
    if (isset($_GET['id'])) 
    {
    
        // Sanitize the id from the input GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Build the SQL query using the filtered id.
        $query = "SELECT * FROM Categories WHERE categoryID = :categoryID";
        $statement = $db->prepare($query);
        $statement->bindValue(':categoryID', $id);

        // Execute the SELECT and get the single row.
        $statement->execute();
        $category = $statement->fetch();

    // If the $id is false.
    } 
    else 
    {
        $id = false;
    }

    // Set the errorFlags to false.
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

            if ($categoryInDB && ($categoryName != $category['categoryName']))
            {
                $categoryTakenFlag = true;
            }
        }

        // If the flags aren't set, we can persist the new category to the DB.
        if(!$categoryFlag && !$categoryTakenFlag)
        {
            // Sanitize the categoryName input.
            $categoryName = filter_input(INPUT_POST, 'categoryName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Has previously been sanitized.
            $categoryID = $id;

            // Create the query for the Update.
            $query = "UPDATE Categories SET categoryName = :categoryName
                     WHERE categoryID = :categoryID";
            
            $statement = $db->prepare($query);

            // Bind the values.
            $statement->bindValue(":categoryName", $categoryName);
            $statement->bindValue(":categoryID", $categoryID);

            // Execute the Update.
            $statement->execute();

            // Send the user back to the adminManageCategories page.
            header("Location: adminManageCategories.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Edit Category - MyBassGallery</title>
</head>
<body>
    <?php if($userType == 1) : ?>
        <?php if($id) : ?>
            <h1> Editing category: <?= $category['categoryName'] ?> </h1>
            <form method="post">
            <label for="categoryName"> Category Name: </label>
            <input name="categoryName" id="categoryNameInput" value="<?= $category['categoryName'] ?>">
            <input type="submit" name="submit" value="Edit Category">
            <?php if($categoryFlag) : ?>
                <p class="error">Please enter a category name. (Maximum 30 characters)</p>
            <?php endif ?>
            <?php if($categoryTakenFlag) : ?>
                <p class="error">Category name already exists.</p>
            <?php endif ?>
        <?php else : ?>
            <p class="error">An error has occurred, please return to the<a href="index.php"> home page</a>.</p>
        <?php endif ?>
    <?php else : ?>
        <h1 class="error"> You do not have permission to use this page. Return to<a href="index.php"> Home.</a></h1>
    <?php endif ?>
</body>
</html>