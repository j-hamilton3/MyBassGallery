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

    // Create the errorFlags for validation.
    $usernameFlag = false;
    $emailFlag = false;
    $usernameTakenFlag = false;

    // If the Delete button was clicked.
    if (isset($_POST['action']) && $_POST['action'] === 'Delete') {

        // Build the SQL query.
        $query = "DELETE FROM Users WHERE userID = :id";

        // Prepare the query and bind the value.
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Execute the statement
        $statement->execute();

        // Send the user back to the index.
        header("Location: adminManageUsers.php");
    }

    // When the form is posted...
    if ($_POST)
    {
        // Check if the email is valid.
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        if (!$email)
        {
            $emailFlag = true;
        }

        // Check if the username is valid.
        
        // If it is zero characters or more than 20 characters.
        if (!$_POST['userName'] || strlen($_POST['userName']) > 20)
        {
            $usernameFlag = true;
        }
        else
        {
            $username = $_POST['userName'];
            
            // Create the SQL Query -> Checks if the matching user in the DB.
            $query = "SELECT * FROM Users WHERE username = :username";
            $statement = $db->prepare($query);

            // Bind the username value.
            $statement->bindValue(":username", $username);

            // Execute the statement.
            $statement->execute();

            // Check if there is a username.
            $userSameName = $statement->fetch(); 

            if ($userSameName && $user['userName'] != $userSameName['userName'] )
            {
                $usernameTakenFlag = true;
            }
        }

        // If no errors were raised, we can persist changes to DB.
        if (!$usernameFlag && !$emailFlag && !$usernameTakenFlag)
        {
            // Sanitize the username.
            $username = filter_input(INPUT_POST, 'userName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Email has already been validated as email.
            $email = $_POST['email'];

            $userType = $_POST['userType'];

            $userID = $id;

            // Create the query for the update.
            $query = "UPDATE Users SET userName = :userName, email = :email, userType = :userType
                      WHERE userID = :userID";
            
            $statement = $db->prepare($query);

            // Bind the values.
            $statement->bindValue(':userName', $username);
            $statement->bindValue(":email", $email);
            $statement->bindValue(":userType", $userType);
            $statement->bindValue(":userID", $userID);

            // Execute the UPDATE.
            $statement->execute();

            // Send the user back to the adminManageUsersPage.
            header("Location: adminManageUsers.php");
        }
    }    
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
                    <a href="adminManageCategories.php">Manage Categories</a>
                <?php endif ?>
                <a class="username"><?= $_SESSION['user']['userName'] ?></a>
                <a href="login.php">Logout</a>
            </div>
        <?php endif ?>   
    </nav>
    <?php if($userType == 1) : ?>
        <?php if($id) : ?>
            <h1> Edit User: <?= $user["userName"] ?> </h1>
            <form method="post">
                <label for="userName">Username:</label>
                <input name="userName" id="edit-userName" value="<?= $user['userName'] ?>">
                <?php if($usernameFlag) : ?>
                    <p class="error">Please enter a username. (Up to 20 characters)</p>
                <?php endif ?>
                <?php if($usernameTakenFlag) : ?>
                    <p class="error">The selected username is already in use.</p>
                <?php endif ?>
                <br>
                <br>
                <label for="email">Email<label>
                <input type="email" name="email" id="edit-email" value="<?= $user['email'] ?>">
                <?php if($emailFlag) : ?>
                    <p class="error"> Please enter a valid email.</p>
                <?php endif ?>
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
        <p class="error"> You do not have permission to use this page. Return to <a href="index.php"> Home.</a></p>
    <?php endif ?>
</body>
</html>