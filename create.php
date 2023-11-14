<?php

/*******w******** 

    Name: James Hamilton
    Date: November 13, 2023
    Description: The create page for MyBassGallery - it allows users to create a post.

****************/

    // There must be a DB connection to continue.
    require('connect.php'); 

    // PHP image resize library.
    require 'C:\xampp\htdocs\a\php-image-resize-master\lib\ImageResize.php';
    require 'C:\xampp\htdocs\a\php-image-resize-master\lib\ImageResizeException.php';

    // Start/Resume the session.
    session_start();

    // Check if user is logged in / in a session.
    $userLoggedIn = false;

    if (!empty($_SESSION))
    {
        $userLoggedIn = true;        
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

        // If there is an invalid upload detected, or a file is not an image, set the imageErrorFlag.
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
            // Initalize selected image to null.
            $image = null;

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
                $og_image = new \Gumlet\ImageResize($temporary_image_path);
                $og_image->save(file_upload_path($image_filename));

                $medium_image = new \Gumlet\ImageResize($temporary_image_path);
                $medium_image->resizeToWidth(400);
                $medium_image->save(file_upload_path($base_filename . '_medium.' . $file_extension));

                $image = (file_upload_path($base_filename . '_medium.' . $file_extension));
            }

            // Create the query for the INSERT.
            $query = "INSERT INTO Post (userID, serialNumber, date, title, content, categoryID, image) VALUES (:userID, :serialNumber, :date, :title, :content, :categoryID, :image )";
            $statement = $db->prepare($query);

            // Bind the values.
            $statement->bindValue(":userID", $userID);
            $statement->bindValue(":serialNumber", $serialNumber);
            $statement->bindValue(":date", $date);
            $statement->bindValue(":title", $title);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":categoryID", $categoryID);
            $statement->bindValue(":image", $image);

            // Execute the INSERT.
            $statement->execute();
        }
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
        <form action="create.php" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input name="title" id="create-title">
            <?php if($titleFlag) : ?>
                <p class="error">The title must be between 1 - 30 characters.</p>
            <?php endif ?>
            <br>
            <br>
            <label for="serialNumber">Serial Number: </label>
            <input name="serialNumber" id="create-serialNumber">
            <?php if($serialNumberFlag) : ?>
                <p class="error">The serial number must be between 1 - 30 characters.</p>
            <?php endif ?>
            <br>
            <br>
            <label for="content"> Post Content: </label>
            <br>
            <br>
            <textarea name="content" id="mytextarea"></textarea>
            <?php if($contentFlag) : ?>
                <p class="error">The post content must be between 1 - 600 characters.</p>
            <?php endif ?>
            <br>
            <br>
            <label for="category"> Category </label>
            <select name="category" id="create-category">
            <?php while($category = $categoriesStatement->fetch()) : ?>
                <option value="<?= $category['categoryID'] ?>"> <?= $category['categoryName'] ?> </option>
            <?php endwhile ?>
            </select>
            <br>
            <br>
            <label for="image">Image: </label>
            <input type='file' name='image' id='image'>
            <?php if($imageFlag) : ?>
                <p class="error">The image is not valid.</p>
            <?php endif ?>
            <br>
            <br>
            <input type="submit" name="submit" value="Create Post">
        </form>
    <?php else : ?>
        <h1 class="error"> You are not logged in, <a href="login.php"> Log in </a> to create a post.</h1>
    <?php endif ?>
    
</body>
</html>
