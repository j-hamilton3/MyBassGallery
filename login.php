<?php
/*******w******** 
    
    Name: James Hamilton
    Date: November 4, 2023
    Description: A webpage for MyBassGallery that allows a user to login with a valid username and password.

****************/
    
    // There must be a DB connection to continue.
    require('connect.php');

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
    <?php if(empty($_SESSION['user'])) : ?>
    <h1>MyBassGallery Login:</h1>
    <form action="login.php" method="post">
        <legend>Please enter your login information:</legend> 
        <br>
        <label for="username">Username</label>
        <br>
        <input name="username" id="username">
        <br>
        <label for="password">Password</label>
        <br>
        <input type="password" name="password" id="password">  
        <input type="submit" id="login-submit" value="Log In"> 
    </form>
    <p> Not a registered user? <a href="register.php">Register Here</a></p>
        <?php if($errorMessage) : ?>
        <h4 class="error"><?= $errorMessage ?></h4>
        <?php endif ?>
    <?php else : ?>
    <h2> You are logged in to your user account. </h2>
    <p> You are logged in as user <?= $_SESSION['user']['userName'] ?> </p>
    <a href="index.php">Return to home.</a>
    <form method="post">
        <input type="submit" name="logout" class="btn_logout" value="log out" />
    </form>
    <?php endif ?>
</body>
</html>