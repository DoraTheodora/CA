<?php
    // Theodora Tataru
    // C00231174 
    // Secure login page
    // 2021
    header("Content-Security-Policy: frame-ancestors 'none'", false);
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    session_cache_limiter('nocache');
    //require 'security_methods.php';

    function create_database()
    {
        if(createDatabase())
        {
            require 'conf.php';
            createMyGuests();
            create_admin();
            createNoGuests();
            createLog();
            createSession();
            echo "<script>
                    alert('The database was created!'); 
                        window.location.href='login.html.php';
                </script>";
            session_unset();
            session_destroy();
        }
        else
        {
            echo "<script>
                    alert('The database was not created!'); 
                        window.location.href='login.html.php';
                </script>";
        }
    }

    function createDatabase()
    {
        require 'conf_admin.php';
        /* Attempt MySQL server connection. Assuming you are running MySQL
        server with default setting (user 'root' with no password) */
        $created = false;
        // Check connection
        if($link === false)
        {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        $created = false;
        // Attempt create database query execution
        $sql = "CREATE DATABASE IF NOT EXISTS theodora";
        //$sql = "CREATE DATABASE theodora";
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
        require 'conf.php';
        $sql = "CREATE TABLE MyGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user VARCHAR(250) NOT NULL,
            passwd VARCHAR(250) NOT NULL,
            salt VARCHAR(250) NOT NULL,
            login_date DATETIME(6) NOT NULL,
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(250) NOT NULL,
            isAdmin INTEGER(5) NOT NULL
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function create_admin()
    {
        require 'conf.php';
        require_once 'security_methods.php';
        $salt = generateSalt();
        $to_hash = $salt."Password1!";
        $hash_pass = hash("sha512", $to_hash);
        $ip = getIPAddress();
        $agent = getClientAgent();
        $user = "admin";
        $isAdmin = 1;

        $sql = "INSERT INTO MyGuests(user, passwd, salt, ip, clientAgent, isAdmin) VALUES (?,?,?,?,?,?)";
        $query = $conn->prepare($sql);
        $query->bind_param("sssssi", $user, $hash_pass, $salt, $ip, $agent, $isAdmin);
        $query->execute();
        $query->close();
    }

    function createNoGuests()
    {
        require 'conf.php';
        $sql = "CREATE TABLE NoGuests
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(500) NOT NULL,
            locked_until DATETIME(6) NOT NULL
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function createLog()
    {
        require 'conf.php';
        $sql = "CREATE TABLE Logs
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            action_performed VARCHAR(250) NOT NULL,
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(500) NOT NULL,
            date_time DATETIME(6),
            outcome VARCHAR(500) NOT NULL
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function createSession()
    {
        require 'conf.php';
        $sql = "CREATE TABLE ActiveSessions
        (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            session_id VARCHAR(250) NOT NULL,
            ip VARCHAR(250) NOT NULL,
            clientAgent VARCHAR(500) NOT NULL,
            date_time DATETIME(6)
        )";
        $query = $conn->prepare($sql);
        $query->execute();
        $query->close();
    }

    function database_exists()
    {
        require 'conf_admin.php';
        $sql = "SHOW DATABASES LIKE 'theodora'";
        $query = $link->prepare($sql);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        if(empty($result))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
?>