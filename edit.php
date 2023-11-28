<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 14, 2023
    Description: The edit post page for MyBassGallery - it allows verified users to edit existing posts.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // Include the utility functions file.
    require('utility.php');

    // Start/Resume the session.
    session_start();

    // PHP image resize library.
    require 'php-image-resize-master\lib\ImageResize.php';
    require 'php-image-resize-master\lib\ImageResizeException.php';

    // Check if GET is set and query the DB.
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
    } 
    else 
    {
        $id = false;
    }

    $pageTitle = "Edit Post - MyBassGallery";

    // Set the title to the title of the post.
    if ($id)
    {
        $pageTitle = "Editing " . $post['title'] . " - MyBassGallery";
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

    // Get list of categories from the categories table.
    $categories = "SELECT * FROM categories;";
    $categoriesStatement = $db->prepare($categories);

    $categoriesStatement->execute();

    // Create flags for error messages.
    $titleFlag = false;
    $serialNumberFlag = false;
    $contentFlag = false;
    $imageFlag = false;

    // If the Delete button was clicked.
    if (isset($_POST['action']) && $_POST['action'] === 'Delete') {
        
        // Remove the old picture from the filesystem.
        if (!empty($post['image'])) {
            $imagePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $post['image'];

            // Check if the file exists before attempting to delete it.
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Sanitize the id from the input GET.
        $deletePostID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // Build the SQL query.
        $query = "DELETE FROM post WHERE postID = :id";

        // Prepare the query and bind the value.
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $deletePostID, PDO::PARAM_INT);

        // Execute the statement
        $statement->execute();

        // Send the user back to the index.
        header("Location: index.php");
    }

    // When the form is posted.
    if ($_POST)
    {
        // Check if the title is valid.
        
        // If it is zero characters or more than 30 characters.
        if (!$_POST['title'] || strlen($_POST['title']) > 30)
        {
            $titleFlag = true;
        }

        // Check if the serial number is valid.

        // If it zero characters or more than 30 characters.
        if (!$_POST['serialNumber'] || strlen($_POST['serialNumber']) > 30)
        {
            $serialNumberFlag = true;
        }

        // Check if the post content is valid.

        // If it zero characters or more than 600 characters.
        if (!$_POST['content'] || strlen($_POST['content']) > 600)
        {
            $contentFlag = true;
        }

        // Check if the image upload is valid.

        // Detects whether the image uploaded or there was an error.
        $upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
        $error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);

        // Sets up a file path to upload the image to.
        function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') 
        {
            $current_folder = dirname(__FILE__);
            
            $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
            
            return join(DIRECTORY_SEPARATOR, $path_segments);
        }

        // Check if the file is an image.
        function file_is_an_image($temporary_path, $new_path) {
            $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
            $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png', 'PNG'];
            
            $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
            $actual_mime_type        = mime_content_type($temporary_path);
            
            $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
            $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
            
            return $file_extension_is_valid && $mime_type_is_valid;
        }

        // If there is an invalid upload detected, or a file is not an image, set the image ErrorFlag.
        if ($upload_detected && $error_detected) 
        {
            $imageFlag = true;
        } 
        else if ($upload_detected && !file_is_an_image($_FILES['image']['tmp_name'], file_upload_path($_FILES['image']['name']))) 
        {
            $imageFlag = true;
        }

         // If no errors were raised, we are ready to add the post to the DB.
        if (!$titleFlag && !$serialNumberFlag && !$contentFlag && !$imageFlag)
        {
            // Sanitize the id from the input GET.
            $postID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
            // Sanitize the title.
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Sanitize the serialNumber.
            $serialNumber = filter_input(INPUT_POST, 'serialNumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Sanitize the content.
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Get the selected category.
            $categoryID = $_POST['category'];

            // Get the id of the user from the session.
            $userID = $_SESSION['user']['userID'];

            // Set the date to the current time.
            $date = date('Y-m-d H:i:s');
            
            // Initalize image to previous value.
            $image = $post['image'];

            // If delete image was checked....
            if (isset($_POST['deleteCheckbox']))
            {
                // Set the image to Null.
                $image = null;
                
                 // Remove the old picture from the filesystem.
                if (!empty($post['image'])) {
                    $imagePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $post['image'];

                    // Check if the file exists before attempting to delete it.
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            // If there was an image uploaded, add it to the uploads folder, then save the file path to the $image variable.
            if ($upload_detected)
            {
                $image_filename        = $_FILES['image']['name'];
                $temporary_image_path  = $_FILES['image']['tmp_name'];
                $new_image_path        = file_upload_path($image_filename); 

                // Get the image extensions and file names separated.
                $image_info = pathinfo($image_filename);
                $file_extension = $image_info['extension'];
                $base_filename = $image_info['filename'];

                // Create resized images.
                // $og_image = new \Gumlet\ImageResize($temporary_image_path);
                // $og_image->save(file_upload_path($image_filename));

                $medium_image = new \Gumlet\ImageResize($temporary_image_path);
                $medium_image->resizeToWidth(400);
                $medium_image->save(file_upload_path($base_filename . '_medium.' . $file_extension));

                $image = "uploads\\" . $base_filename . '_medium.' . $file_extension;
            }

            // Create the query for the INSERT.
            $query = "UPDATE Post SET userID = :userID, serialNumber = :serialNumber, date = :date, title = :title, 
                                               content = :content, categoryID = :categoryID, image = :image
                                               WHERE postID = :postID";

            $statement = $db->prepare($query);

            // Bind the values.
            $statement->bindValue(':postID', $postID);
            $statement->bindValue(":userID", $userID);
            $statement->bindValue(":serialNumber", $serialNumber);
            $statement->bindValue(":date", $date);
            $statement->bindValue(":title", $title);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":categoryID", $categoryID);
            $statement->bindValue(":image", $image);

            // Execute the INSERT.
            $statement->execute();

            // Send the user back to the index.
            header("Location: index.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tiny.cloud/1/4hz58kktrqrm6b4oka9ltipbvhgso4p1jqwk35j9czjmbtvb/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
        selector: '#myedittextarea'
      });
    </script>
</head>
<body>
    <?php if ($id && ($editPermission || $userCreatedThisPost)) : ?>
        <h1> Edit Post : <?= $post['title'] ?> </h1>
        <form method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input name="title" id="edit-title" value="<?= $post['title'] ?>">
            <?php if($titleFlag) : ?>
                <p class="error">The title must be between 1 - 30 characters.</p>
            <?php endif ?>
            <br>
            <br>
            <label for="serialNumber">Serial Number: </label>
            <input name="serialNumber" id="edit-serialNumber" value="<?= $post['serialNumber'] ?>">
            <?php if($serialNumberFlag) : ?>
                <p class="error">The serial number must be between 1 - 30 characters.</p>
            <?php endif ?>
            <br>
            <br>
            <label for="content"> Post Content: </label>
            <br>
            <br>
            <textarea name="content" id="myedittextarea"><?= $post['content'] ?></textarea>
            <?php if($contentFlag) : ?>
                <p class="error">The post content must be between 1 - 600 characters.</p>
            <?php endif ?>
            <br>
            <br>
            <label for="category"> Category </label>
            <select name="category" id="edit-category">
            <?php while($category = $categoriesStatement->fetch()) : ?>
                <option value="<?= $category['categoryID'] ?>" <?php echo ($category['categoryID'] == $post['categoryID']) ? 'selected' : ''; ?>>
                    <?= $category['categoryName'] ?>
                </option>
            <?php endwhile ?>
            </select>
            <br>
            <br>
            <?php if(!empty($post["image"])) : ?>
                <label for="deleteCheckbox">Delete previous image:</label>
                <input type="checkbox" id="deleteCheckbox" name="deleteCheckbox">
                <br>
                <br>
            <?php endif ?>
            <label for="image">New Image: </label>
            <input type='file' name='image' id='image'>
            <?php if($imageFlag) : ?>
                <p class="error">The image is not valid.</p>
            <?php endif ?>
            <br>
            <br>
            <input type="submit" name="submit" value="Edit Post">
            <input type="submit" name="action" value="Delete" onclick="return confirm('Are you sure you wish to delete this post?')" > 
        </form>
    <?php else: ?>
        <p class="error-message">An error has occurred, please return to the<a href="index.php"> home page</a>.</p>
    <?php endif ?>
</body>
</html>