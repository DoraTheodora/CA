<?php
    include 'conf.php';
    session_start();
    if(isset($_POST['username']) && isset($_POST['pass']) && isset($_SESSION['login_attempts']) && isset($_SESSION['ip']) && isset($_SESSION['clientAgent']))
    {
        $username = $_POST['username'];
        $password = $_POST['pass'];
    }
    else
    {
        header('Location: index.php');
    }
   
    if (!$conn) 
    {
        die("Connection failed: " . mysqli_connect_error());
    }

    if(userExistsInTheDatabase($username, $conn))
    {
        echo "<script>alert('This username is already in use. Please try another one')</script>";
        header("Refresh:0 url=signup.html");
    }
    else
    {
        createAccount($username, $password, $conn);
    }
   
    // * Functions ---------------------------------------------------------------------------------------------------------
    function createAccount($username, $password, $conn)
    {
        $salt = generateSalt();
        $to_hash = $password . $salt;
        $hash_pass = password_hash($to_hash, PASSWORD_ARGON2I);
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];

        $sql = "INSERT INTO MyGuests(user, passwd, salt, ip, clientAgent) VALUES ('$username', '$hash_pass', '$salt', '$ip', '$agent')";
        if(mysqli_query($conn, $sql))
        {
            echo "<script>alert('Account created!')</script>";
            header('Refresh:0 url=index.php');
        } 
        else 
        {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    
        mysqli_close($conn);
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

    function userExistsInTheDatabase($username, $conn)
    {
        $SQL = "SELECT * FROM MyGuests WHERE user='$username'";
        $users = mysqli_query($conn, $SQL);
        if(mysqli_num_rows($users) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
?>  