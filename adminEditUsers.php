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

?>