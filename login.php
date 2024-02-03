<?php
/*******w********

    Name: James Hamilton
    Date: November 4, 2023
    Description: A webpage for MyBassGallery that allows a user to login with a valid username and password.

****************/

    // There must be a DB connection to continue.
    require('connect.php');

    // Include the utility.php functions.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // Error message to be displayed based on specific error.
    $errorMessage = "";

    // If there is an empty username or password, display an error.
    if($_POST && (empty($_POST['username']) || empty($_POST['password'])))
    {
        $errorMessage = "Please fill out both the username and password fields.";
    }
    else if (!empty($_POST))
    {
        // Sanitization considered in the future?

        $username = $_POST['username'];
        $password = $_POST['password'];

        // Create the SQL Query -> Checks if the matching user in the DB.
        $query = "SELECT * FROM Users WHERE username = :username";
        $statement = $db->prepare($query);

        // Bind the username value.
        $statement->bindValue(":username", $username);

        // Execute the statement.
        $statement->execute();

        // Check if there is a username.
        $user = $statement->fetch();

        // If there is a user...
        if ($user)
        {
           // Check if the hashed/salted password matches.
            if (password_verify($password, $user['password']))
            {
                $_SESSION['user'] = $user;
            }
            else
            {
                $errorMessage = "Incorrect password.";
            }
        }
        else
        {
            $errorMessage = "User not found.";
        }
    }

    // Functionality for log out button.
    if (array_key_exists('logout', $_POST))
    {
        $_SESSION = [];
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login - MyBassGallery</title>
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
            </div>
        <?php endif ?>
    </nav>
    <?php if(empty($_SESSION['user'])) : ?>
    <h1 id="login-title">MyBassGallery Login</h1>
    <form action="login.php" method="post" id="login-form">
        <legend>Please enter your login information:</legend>
        <br>
        <label for="username">Username:</label>
        <br>
        <input name="username" id="username">
        <br>
        <label for="password">Password:</label>
        <br>
        <input type="password" name="password" id="password">
        <input type="submit" id="login-submit" value="Log In">
    </form>
        <?php if($errorMessage) : ?>
        <h4 class="error" id="login-error"><?= $errorMessage ?></h4>
        <?php endif ?>
    <p id="register-message"> Not a registered user? <a href="register.php">Register Here</a></p>
    <?php else : ?>
    <div id="logged-in">
        <h2> You are logged in as user <?= $_SESSION['user']['userName'] ?>. </h2>
        <a href="index.php">Return to home.</a>
        <form method="post" id="logged-in-form">
            <input type="submit" name="logout" class="btn_logout" value="log out" />
        </form>
    </div>
    <?php endif ?>
</body>
</html>