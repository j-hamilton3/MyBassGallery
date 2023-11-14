<?php

/*******w******** 
    
    Name: James Hamilton
    Date: November 13, 2023
    Description: A class that includes utility functions.

****************/
    
// Function to get a user's name by their corresponding ID.
    function getUserByID($userID, $db)
    {
        $userQuery = "SELECT * FROM users WHERE userID = " . $userID . ";";

        $userStatement = $db->prepare($userQuery);

        $userStatement->execute();

        return $userStatement->fetch();
    }

    // Function to get a category name by corresponding ID.
    function getCategoryByID($categoryID, $db)
    {
        $categoryQuery = "SELECT * FROM categories WHERE categoryID = " . $categoryID . ";";

        $categoryStatement = $db->prepare($categoryQuery);

        $categoryStatement->execute();

        return $categoryStatement->fetch();
    }

    // Function to check what user type is in the current session.
    function checkUserType()
    {
        // 0 is returned if no user is found in the session.
        $type = 0;

        if (!empty($_SESSION['user']))
        {
            $type = $_SESSION['user']['userType'];
        }

        return $type;
    }
?>