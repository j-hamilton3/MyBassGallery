<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 4, 2023
    Description: A webpage for MyBassGallery that allows a user to create a profile.

****************/
    
    // There must be a DB connection to continue.
    require('connect.php'); 

    // Start/Resume the session.
    session_start();

    // A flag to indicate a user has successfully been created.
    $userCreatedFlag = false;

    // Create flags for error messages.
    $emailFlag = false;
    $usernameFlag = false;
    $usernameTakenFlag = false;
    $passwordFlag = false;
    $passwordMatchFlag = false;

    // When the form is posted.
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
        if (!$_POST['username'] || strlen($_POST['username']) > 20)
        {
            $usernameFlag = true;
        }
        else
        {
            $username = $_POST['username'];
            
            // Create the SQL Query -> Checks if the matching user in the DB.
            $query = "SELECT * FROM Users WHERE username = :username";
            $statement = $db->prepare($query);

            // Bind the username value.
            $statement->bindValue(":username", $username);

            // Execute the statement.
            $statement->execute();

            // Check if there is a username.
            $user = $statement->fetch(); 

            if ($user)
            {
                $usernameTakenFlag = true;
            }
        }

        // Check if the password is valid.
        
        // If it is zero characters or more than 20 characters.
        if (!$_POST['password'] || strlen($_POST['password']) > 20)
        {
            $passwordFlag = true;        
        }

        // Check if the passwords match.
        
        if (!$_POST['password2'] || $_POST['password'] != $_POST['password2'])
        {
            $passwordMatchFlag = true;
        }

        // If there were no errors raised, we are ready to add the new user to the DB.
        if (!$emailFlag && !$usernameFlag && !$usernameTakenFlag && !$passwordFlag && !$passwordMatchFlag)
        {
            // Sanitize the username.
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Email has already been validated as email.
            $email = $_POST['email'];

            // To be salted and hashed using PHP's password hash function.
            $password = $_POST['password'];

            // The user type for a registered user is 3 -> this is what is assigned by default.
            $userType = 3;

            // Create the query for the insert.
            $query = "INSERT INTO Users (userType, userName, email, password) VALUES (:userType, :username, :email, :password)";
            $statement = $db->prepare($query);

            // Bind the values.
            $statement->bindValue(":userType", $userType);
            $statement->bindValue(":username", $username);
            $statement->bindValue(":email", $email);
            $statement->bindValue(":password", $password);

            // Execute the INSERT.
            $statement->execute();

            // Set the userCreated flag.
            $userCreatedFlag = true;
        }
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Register - MyBassGallery</title>
</head>
<body>
    <?php if(!$userCreatedFlag) : ?>
    <h1>Register for a MyBassGallery account:</h1>
    <form action="register.php" method="post">
    <legend>Please enter the following information:</legend> 
    <br>
    <label for="email">Email<label>
    <br>
    <input type="email" name="email" id="register-email">
    <?php if($emailFlag) : ?>
    <p class="error"> Please enter a valid email.</p>
    <?php endif ?>
    <br>
    <label for="username">Username</label>
    <br>
    <input name="username" id="register-username">
    <?php if($usernameFlag) : ?>
    <p class="error">Please enter a username (Up to 20 characters).</p>
    <?php endif ?>
    <?php if($usernameTakenFlag) : ?>
    <p class="error">The selected username is already in use.</p>
    <?php endif ?>
    <br>
    <label for="password">Password</label>
    <br>
    <input type="password" name="password" id="register-password">
    <?php if($passwordFlag) : ?>
    <p class="error">Please enter a password (Up to 20 characters).</p>
    <?php endif ?>
    <br>
    <label for="password2">Re-enter your password:</label>
    <br>
    <input type="password" name="password2" id="register-password2">
    <?php if($passwordMatchFlag) : ?>
    <p class="error">The passwords must match.</p>
    <?php endif ?>
    <br>
    <br>  
    <input type="submit" id="register-submit" value="Register"> 
    </form>
    <?php endif ?>
    <?php if($userCreatedFlag) : ?>
    <h1> User successfully created! </h1>
    <?php endif ?>
</body>
</html>