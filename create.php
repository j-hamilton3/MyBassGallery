<?php

/*******w******** 

    Name: James Hamilton
    Date: November 13, 2023
    Description: The create page for MyBassGallery - it allows users to create a post.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Start/Resume the session.
    session_start();

    // Check if user is logged in / in a session.
    $userLoggedIn = false;

    if (!empty($_SESSION))
    {
        $userLoggedIn = true;        
    }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create A Post - MyBassGallery</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tiny.cloud/1/4hz58kktrqrm6b4oka9ltipbvhgso4p1jqwk35j9czjmbtvb/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
        selector: '#mytextarea'
      });
    </script>
</head>
<body>
    <?php if($userLoggedIn) : ?>
        <h1> Create a post: </h1>
        <form action="create.php" method="post">
            <label for="title">Title:</label>
            <input name="title" id="create-title">
            <br>
            <br>
            <label for="serialNumber">Serial Number: </label>
            <input name="serialNumber" id="create-serialNumber">
            <br>
            <br>
            <label for="content"> Post Content: </label>
            <br>
            <br>
            <textarea id="mytextarea"></textarea>
            <br>
            <br>
            <label for="category"> Category </label>
        </form>
    <?php else : ?>
        <h1 class="error"> You are not logged in, <a href="login.php"> Log in </a> to create a post.</h1>
    <?php endif ?>
    
</body>
</html>
