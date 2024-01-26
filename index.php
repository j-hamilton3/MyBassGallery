<?php

/*******w********

    Name: James Hamilton
    Date: November 4, 2023
    Description: The main page for MyBassGallery - it displays user created posts in a feed.

****************/

    // There must be a DB connection to continue.
    require('connect.php');

    // Include the functions from the utility.php file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // Get all the posts from the Post table.
    $query = "SELECT * FROM post ORDER BY date DESC;";

       // Make a prepare statement with the query.
    $statement = $db->prepare($query);

    // Execute the statement.
    $statement->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Home - MyBassGallery</title>
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
    <?php while($post = $statement->fetch()) : ?>
        <div class="post">
            <h2><a href="post.php?id=<?= $post['postID'] ?>"><?= $post['title'] ?></a></h2>
            <p><b>Serial Number:</b> <?= $post['serialNumber'] ?></p>
            <?php if(!empty($post['image'])) : ?>
                <img src="<?= $post['image'] ?>" class="post-image">
            <?php endif ?>
            <hr>
            <p class="post-content"><?= html_entity_decode($post['content']) ?></p>
            <p class="post-category"><b>Category:</b> <?= getCategoryByID($post['categoryID'], $db)['categoryName'] ?></p>
            <p><b>By User:</b> <?= getUserByID($post['userID'], $db)['userName'] ?></p>
            <p><b>Created On:</b> <?= $post['date'] ?></p>
        </div>
    <?php endwhile ?>
</body>
</html>