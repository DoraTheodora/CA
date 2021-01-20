<?php
    //TODO hash and salt the password
    if(isset($_POST['username']) && isset($_POST['pass']))
    {
        $username = $_POST['username'];
        $password = $_POST['pass'];
        validateUser($username, $password);
    }
    else
    {
        header('Location: index.html');
    }

    // * Methods
    function validateUser($username, $password)
    {
        // ! Needs to be removed from here
        $conn = mysqli_connect("localhost:3306", "root", "", "tt");

        $sql = "SELECT user, passwd FROM MyGuests WHERE user='$username' AND passwd='$password' ";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0)
        {
            if (session_status() == PHP_SESSION_NONE)
            {
                session_start();
                $_SESSION["name"] = $username;
                header('Location: profile.html');
            }
        }
        else
        {
            echo "Bad credentials";
        }
    }

    function generateRandomSessionID()
    {
        $length = 64;
        $characters = '0123456789!@#$%^&*()|}{:[]<>?/.,|\~`abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $salt = "";
        for($i = 0 ; $i < $length; $i++)
        {
            $index = rand(0, strlen($characters) -1);
            $salt .= $characters[$index];
        }
        return $salt;
    }
?>