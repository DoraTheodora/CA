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

        $sql = "SELECT salt, passwd FROM MyGuests WHERE user='$username'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0)
        {
            $details = mysqli_fetch_assoc($result); 
            //echo "PASSWORD: " . $password ."<br>"; 
            $salt = $details['salt'];
            $pass = $details['passwd'];
            //echo "SALT: " . $salt ."<br>";
            $to_hash = $password . $salt;
            //echo "HASH + PASSWORD: " . $password ."<br>";
            if(password_verify($to_hash, $pass))
            {
                $SQL = "SELECT user FROM MyGuests WHERE user='$username' AND passwd='$pass'";
                $login_user = mysqli_query($conn, $SQL);
                if(mysqli_num_rows($login_user) > 0)
                {
                    session_unset();
                    session_destroy();
                    $sql = "UPDATE MyGuests SET login_date = CURRENT_TIMESTAMP WHERE user='$username' AND passwd='$hash_pass'";
                    $result = mysqli_query($conn, $sql);
                    session_id(generateRandomSessionID());
                    session_start(); 
                    $_SESSION["id_s"] = session_id();
                    $_SESSION["name"] = $username;        
                    header('Location: profile.php');
                }
                else
                {
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($login_user);
                    session_destroy();
                }  
            }  
            else
            {
                session_destroy();
                echo "Bad credentials";
            }  
        }
        else
        {
            echo "First ELSE";
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