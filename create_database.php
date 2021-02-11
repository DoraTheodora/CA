<?php

    if(createDatabase())
    {
        createMyGuests();
        createNoGuests();
        echo "<script>
                alert('The database was created!'); 
                    window.location.href='index.php';
            </script>";
    }
    else
    {
        echo "<script>
                alert('The database was not created!'); 
                    window.location.href='index.php';
            </script>";
    }

    function createDatabase()
    {
        include 'conf.php';
        /* Attempt MySQL server connection. Assuming you are running MySQL
        server with default setting (user 'root' with no password) */
        $created = false;
        // Check connection
        if($link === false)
        {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
    
        // Attempt create database query execution
        $sql = "CREATE DATABASE TT";
        $created = false;
        if(mysqli_query($link, $sql))
        {
            $created = true;
        } 
        else
        {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
        }
        return $created;
    }
    
    function createMyGuests()
    {  
        include 'conf.php';
        $sql = "CREATE TABLE MyGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user VARCHAR(120) NOT NULL,
            passwd VARCHAR(120) NOT NULL,
            salt VARCHAR(120) NOT NULL,
            login_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            locked_until TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ip VARCHAR(120) NOT NULL,
            clientAgent VARCHAR(120) NOT NULL
        )";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    }

    function createNoGuests()
    {
        include 'conf.php';
        $sql = "CREATE TABLE NoGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(120) NOT NULL,
            clientAgent VARCHAR(120) NOT NULL,
            locked_until TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    }
    
    // Close connection
    mysqli_close($link);
?>