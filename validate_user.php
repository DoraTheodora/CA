<?php
    session_start();
    //TODO hash and session_id the password
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
            session_unset();
            session_destroy();
            $sql = "UPDATE MyGuests SET login_date = CURRENT_TIMESTAMP WHERE user='$username' AND passwd='$password'";
            $result = mysqli_query($conn, $sql);
            session_id(generateRandomSessionID());
            session_start(); 
            $_SESSION["id_s"] = session_id();
            $_SESSION["name"] = $username;        
            #echo $_SESSION["id_s"];
            header('Location: profile.php');
        }
        else
        {
            session_destroy();
            echo "Bad credentials";
        }
    }

    function generateRandomSessionID()
    {
        $length = 64;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $session_id = "";
        for($i = 0 ; $i < $length; $i++)
        {
            $index = rand(0, strlen($characters) -1);
            $session_id .= $characters[$index];
        }
        return $session_id;
    }
?>