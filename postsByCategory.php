<?php

/*******w********

    Name: James Hamilton
    Date: November 14, 2023
    Description: A webpage for MyBassGallery that allows users to view a specific category.

****************/

    // There must be a DB connection to continue.
    require('connect.php');

    // Include the utility functions file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    if (isset($_GET['id'])) {

        // Sanitize the id from the input GET.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Build the SQL query using the filtered id.
        $query = "SELECT * FROM post WHERE categoryID = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);


        $statement->execute();

        // If the $id is false.
    }
    else
    {
        $id = false;
    }

    $pageTitle = "Category - MyBassGallery";

    // Set the title to the title of the post.
    if ($id)
    {
        $pageTitle = getCategoryByID($id, $db)['categoryName'] . " - MyBassGallery";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="styles.css">
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
    <?php if($id) : ?>
        <h1 id="category-posts-title">Category: <?= getCategoryByID($id, $db)['categoryName']  ?> Basses</h1>
        <?php while($post = $statement->fetch()) : ?>
        <div class="post">
            <h2><a href="post.php?id=<?= $post['postID'] ?>"><?= $post['title'] ?></a></h2>
            <p><b>Serial Number:</b> <?= $post['serialNumber'] ?></p>
            <?php if(!empty($post['image'])) : ?>
                <img src="<?= $post['image'] ?>" class="post-image">
            <?php endif ?>
            <p><?= html_entity_decode($post['content']) ?></p>
            <p><b>Category:</b> <?= getCategoryByID($post['categoryID'], $db)['categoryName'] ?></p>
            <p><b>Post by user:</b> <?= getUserByID($post['userID'], $db)['userName'] ?></p>
            <p><b>Created On:</b> <?= date("F j, Y g:i A", strtotime($post['date'])) ?></p>
        </div>
    <?php endwhile ?>
    <?php else : ?>
        <p class="error">An error has occurred, please return to the <a href="index.php"> home page</a>.</p>
    <?php endif ?>
</body>
</html>