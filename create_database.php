<?php
    session_start();
    if(createDatabase())
    {
        createMyGuests();
        create_admin();
        createNoGuests();
        createLog();
        echo "<script>
                alert('The database was created!'); 
                    window.location.href='index.php';
            </script>";
        session_unset();
        session_destroy();
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
        include 'conf_admin.php';
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
        $query = $link->prepare($sql);
        if($query->execute())
        {
            $created = true;
        } 
        else
        {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
        }
        $query->close();
        return $created;
    }
    
    function createMyGuests()
    {  
        include 'conf.php';
        $sql = "CREATE TABLE MyGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user VARCHAR(250) NOT NULL,
            passwd VARCHAR(250) NOT NULL,
            salt VARCHAR(250) NOT NULL,
            login_date DATETIME(5),
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(250) NOT NULL
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function create_admin()
    {
        include 'conf.php';
        $salt = generateSalt();
        $to_hash = "Password1!" . $salt;
        $hash_pass = password_hash($to_hash, PASSWORD_ARGON2I);
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];
        $user = "admin";

        $sql = "INSERT INTO MyGuests(user, passwd, salt, ip, clientAgent) VALUES (?,?,?,?,?)";
        $query = $conn->prepare($sql);
        $query->bind_param("sssss", $user, $hash_pass, $salt, $ip, $agent);
        $query->execute();
        $query->close();
    }

    function createNoGuests()
    {
        include 'conf.php';
        $sql = "CREATE TABLE NoGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(500) NOT NULL,
            locked_until DATETIME(5)
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function createLog()
    {
        include 'conf.php';
        $sql = "CREATE TABLE Logs
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            action_performed VARCHAR(250) NOT NULL,
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(500) NOT NULL,
            date_time DATETIME(5),
            outcome VARCHAR(500) NOT NULL
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function generateSalt()
    {
        $length = 96;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $salt = "";
        for($i = 0 ; $i < $length; $i++)
        {
            $index = rand(0, strlen($characters) -1);
            $salt .= $characters[$index];
        }
        return $salt;
    } 
?>