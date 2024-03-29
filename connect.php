<?php

/*******w********

    Name: James Hamilton
    Date: November 4, 2023
    Description: Created to ensure the user is connected to the Database.

****************/

     define('DB_DSN','mysql:host=localhost;dbname=serverside;charset=utf8');
     define('DB_USER','root'); // Previously 'serveruser'
     define('DB_PASS',''); // Previously 'gorgonzola7!'

    //  PDO is PHP Data Objects
    //  mysqli <-- BAD.
    //  PDO <-- GOOD.
     try {
         // Try creating new PDO connection to MySQL.
         $db = new PDO(DB_DSN, DB_USER, DB_PASS);
         //,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
     } catch (PDOException $e) {
         print "Error: " . $e->getMessage();
         die(); // Force execution to stop on errors.
         // When deploying to production you should handle this
         // situation more gracefully. ¯\_(ツ)_/¯
     }
 ?>