<?php
    include 'conf.php';
    /* Attempt MySQL server connection. Assuming you are running MySQL
    server with default setting (user 'root' with no password) */
    // ! Needs to be removed from here
    $created = false;
    // Check connection
    if($conn === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    
    // Attempt create database query execution
    $sql = "CREATE DATABASE TT";
    $created = false;
    if(mysqli_query($link, $sql))
    {
        echo "Database created successfully";
        $created = true;
        $sql = "CREATE TABLE MyGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user VARCHAR(120) NOT NULL,
            passwd VARCHAR(120) NOT NULL,
            salt VARCHAR(120) NOT NULL,
            login_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            locked_until TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        if(mysqli_query($conn, $sql))
        {
            echo "\n Table created!";
        }

        mysqli_close($conn);
    } 
    else
    {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }
    
    
    // Close connection
    mysqli_close($link);
?>