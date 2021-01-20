<?php
    /* Attempt MySQL server connection. Assuming you are running MySQL
    server with default setting (user 'root' with no password) */
    // ! Needs to be removed from here
    $link = mysqli_connect("localhost:3306", "root", "");
    $created = false;
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    // Attempt create database query execution
    $sql = "CREATE DATABASE TT";
    $created = false;
    if(mysqli_query($link, $sql))
    {
        echo "Database created successfully";
        $created = true;
    } 
    else
    {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
    
    if($created)
    {
        $sql = "CREATE TABLE MyGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user VARCHAR(60) NOT NULL,
            passwd VARCHAR(60) NOT NULL,
            salt VARCHAR(60) NOT NULL,
            reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

        )";
        // ! Needs to be removed from here
        $conn = mysqli_connect("localhost:3306", "root", "", "tt");
        if(mysqli_query($conn, $sql))
        {
            echo "\n Table created!";
        }

        mysqli_close($conn);
    }
    
    // Close connection
    mysqli_close($link);
?>