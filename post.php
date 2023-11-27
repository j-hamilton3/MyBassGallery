<?php

/*******w******** 

    Name: James Hamilton
    Date: November 13, 2023
    Description: The post page for MyBassGallery -> it allows users to view a full post and its comments.

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
        $query = "SELECT * FROM post WHERE postID = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Execute the SELECT and get the single row.
        $statement->execute();
        $post = $statement->fetch();

    // If the $id is false.
    } else {
        $id = false;
    }

    $pageTitle = "Post - MyBassGallery";

    // Set the title to the title of the post.
    if ($id)
    {
        $pageTitle = $post['title'] . " - MyBassGallery";
    }

    // Checks the user's type in the current session.
    $userType = checkUserType();

    $editPermission = false;

    // If a user is a moderator (2) or admin (1), they have permission to edit this post.
    if ($userType == 1 || $userType == 2)
    {
        $editPermission = true;
    }

    $userCreatedThisPost = false;

    // If the user who created this post is in the session, they have permission to edit this post.
    if ($id && !empty($_SESSION['user']))
    {
        $userInSession = $_SESSION['user']['userID'];

        $userWhoCreatedPost = $post['userID'];

        if ($userInSession == $userWhoCreatedPost)
        {
            $userCreatedThisPost = true;
        }
    }

    // Error flag for comment content.
    $contentFlag = false;

    // If a comment is Posted.
    if ($_POST)
    {
        // Check if the content is 0 and more than 300 characters.
        if (!$_POST['content'] || strlen($_POST['content']) > 300)
        {
            // Set the content flag.
            $contentFlag = true;
        }

        // If the contentFlag is not set off persist comment to DB.
        if (!$contentFlag)
        {
            $userID = $_SESSION['user']['userID'];

            // Previously sanitized.
            $postID = $id;

            // Sanitize content.
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Get the current date.
            $date = date('Y-m-d H:i:s');

            // Create the query for the INSERT.
            $query = "INSERT INTO Comment (userID, postID, content, date) VALUES (:userID, :postID, :content, :date)";

            $statement = $db->prepare($query);

            // Bind the values.
            $statement->bindValue(":userID", $userID);
            $statement->bindValue(":postID", $postID);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":date", $date);
        
            // Execute the INSERT.
            $statement->execute();
        }
    }

    // If the id is valid, we can retrieve the comments for the page.
    if ($id)
    {
        // Build the SQL query using the filtered id.
        $commentQuery = "SELECT * FROM comment WHERE postID = :id ORDER BY date DESC";
        $commentStatement = $db->prepare($commentQuery);
        $commentStatement->bindValue(':id', $id);
 
        // Execute the statement.
        $commentStatement->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title><?= $pageTitle ?></title>
</head>
<body>
    <nav>
        <h1>Post</h1>
        <?php if(empty($_SESSION)) : ?>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <?php else : ?>
        <a href="index.php">Home</a>
        <a href="create.php">Create a post</a>
        <a href="profile.php?userID=<?= $_SESSION['user']['userID'] ?>"><?= $_SESSION['user']['userName'] ?></a>
        <a href="login.php">Logout</a>
        <?php endif ?>   
    </nav>
    <?php if($id) : ?>
        <div class="post">
            <h2><?= $post['title'] ?></h2>
            <p>Serial Number: <?= $post['serialNumber'] ?></p>
            <?php if(!empty($post['image'])) : ?>
                <img src="<?= $post['image'] ?>" class="post-image">
            <?php endif ?>
            <p><?= html_entity_decode($post['content']) ?></p>
            <p>Category: <?= getCategoryByID($post['categoryID'], $db)['categoryName'] ?></p>
            <p>Post by user: <?= getUserByID($post['userID'], $db)['userName'] ?></p>
            <p>Created on: <?= $post['date'] ?></p>
            <?php if($editPermission || $userCreatedThisPost) : ?>
                <a href="edit.php?id=<?= $post['postID'] ?>">Edit this post</a>
            <?php endif ?>
            <?php if($editPermission) : ?>
                <h2>You can edit this post!</h2>
            <?php endif ?>
            <?php if($userCreatedThisPost) : ?>
                <h2>You created this post!</h2>
            <?php endif ?>
        </div>
        <?php while($comment = $commentStatement->fetch()) : ?>
            <div class="comment">
                <h3>User: <?= getUserByID($comment['userID'], $db)['userName'] ?></h3>
                <p><?= $comment['content'] ?></p>
                <p><?= $comment['date'] ?></p>
                <p></p>
            </div>
        <?php endwhile ?>
        <?php if($userType != 0) : ?>
            <h2>Submit a comment, <?= $_SESSION['user']['userName'] ?>:</h2>
            <form method="post">
                <label for="content">Comment: </label>
                <br>
                <br>
                <input name="content" id="commentContent">
                <?php if($contentFlag) : ?>
                    <p class="error">Please enter a comment. (Maximum 300 characters)</p>
                <?php endif ?>
                <br>
                <br>
                <input type="submit" name="submit" value="Create Post">
            </form>
        <?php endif ?>
    <?php else : ?>
            <p class="error-message">An error has occurred, please return to the<a href="index.php"> home page</a>.</p>
    <?php endif ?>
</body>
</html>