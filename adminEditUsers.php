<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 25, 2023
    Description: A webpage for MyBassGallery that allows admins to edit users.

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
    if (isset($_GET['id'])) {
        
        // Sanitize the id from the input GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Build the SQL query using the filtered id.
        $query = "SELECT * FROM users WHERE userID = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Execute the SELECT and get the single row.
        $statement->execute();
        $user = $statement->fetch();

    // If the $id is false.
    } 
    else 
    {
        $id = false;
    }

    // When the form is posted...
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Edit User - MyBassGallery</title>
</head>
<body>
    <?php if($userType == 1) : ?>
        <?php if($id) : ?>
            <h1> Edit User: <?= $user["userName"] ?> </h1>
            <form method="post">
                <label for="userName">Username:</label>
                <input name="userName" id="edit-userName" value="<?= $user['userName'] ?>">
                <br>
                <br>
                <label for="email">Email<label>
                <input type="email" name="email" id="edit-email" value="<?= $user['email'] ?>">
                <br>
                <br>
                <label for="userType"> User Type </label>
                <select name="userType" id="edit-userType">
                    <option value="3" <?= ($user['userType'] == 3) ? 'selected' : '' ?>>Registered</option>
                    <option value="2" <?= ($user['userType'] == 2) ? 'selected' : '' ?>>Moderator</option>
                    <option value="1" <?= ($user['userType'] == 1) ? 'selected' : '' ?>>Admin</option>
                </select>
                <br>
                <br>
                <input type="submit" name="submit" value="Edit User">
                <input type="submit" name="action" value="Delete" onclick="return confirm('Are you sure you wish to delete this user?')" > 
            </form>   
        <?php else : ?>
            <p class="error">An error has occurred, please return to the<a href="index.php"> home page</a>.</p>
        <?php endif ?>  
    <?php else : ?>
        <h1 class="error"> You do not have permission to use this page. Return to<a href="index.php"> Home.</a></h1>
    <?php endif ?>
</body>
</html>