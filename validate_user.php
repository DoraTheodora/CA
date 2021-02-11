<?php
    session_start();
    if(isset($_POST['username']) && isset($_POST['pass']) && isset($_SESSION['login_attempts']) && isset($_SESSION['ip']) && isset($_SESSION['clientAgent']))
    {
        $username = $_POST['username'];
        $password = $_POST['pass'];
        check_if_user_is_locked($username, $password);
    }
    else
    {
        header('Location: index.php');
    }

// * Functions ---------------------------------------------------------------------------------------------------------
    function check_if_non_user_is_blocked()
    {
        include 'conf.php';

        $ip = $_SESSION['ip'];
        $userAgent = $_SESSION['clientAgent'];
        $sql = "SELECT locked_until FROM NoGuests WHERE ip='$ip' AND clientAgent='$userAgent'";
        $suspiciousUser = mysqli_query($conn, $sql);
        $start_date_time = date("Y-m-d H:i:s"); 
        echo mysqli_num_rows($suspiciousUser); 
        if(mysqli_num_rows($suspiciousUser) > 0)
        {
            $details = mysqli_fetch_assoc($suspiciousUser);
            if($details['locked_until'] >  $start_date_time)
            {
                echo "<script>
                alert('Sorry you are still locked out !'); 
                    window.location.href='index.php';
                </script>";
            }
            else
            {
                echo "Here";
            }
        }
        else
        {
            echo "Nothing from the database";
        }
    }

    function check_if_user_is_locked($username, $password)
    {
        include 'conf.php';
        $sql = "SELECT locked_until FROM MyGuests WHERE user='$username'";
        $suspiciousUser = mysqli_query($conn, $sql);
        $start_date_time = date("Y-m-d H:i:s");
        if(mysqli_num_rows($suspiciousUser) > 0)
        {
            $details = mysqli_fetch_assoc($suspiciousUser);
            if($details['locked_until'] >  $start_date_time)
            {
                echo "<script>
                alert('Sorry you are still locked out !'); 
                    window.location.href='index.php';
                </script>";
            }
            else
            {
                validateUser($username, $password);
            }
        }
        else
        {
            //TODO: This is not working!
            validateUser($username, $password);
        }
    }

    function lockFiveMinutes()
    {
        $start_date_time = date("Y-m-d H:i:s");
        $locked_until = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($start_date_time)));
        return $locked_until;
    }


    function validateUser($username, $password)
    {
        include 'conf.php';
        $_SESSION['login_attempts']++;
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
                logIn($username, $pass, $conn);
            }  
            else
            {
                echo "<script>
                        alert('Username or password incorrect! Please try again'); 
                     </script>";
                blockUser($username, $conn);
            }  
        }
        else
        {
            echo "<script>
                    alert('Username or password incorrect! Please try again'); 
                    window.location.href='index.php';
                </script>";
        }
    }

    function blockUser($username, $conn)
    {
        if($_SESSION['login_attempts']%3 == 0)
        {
            $sql = "SELECT locked_until FROM MyGuests WHERE user='$username'";
            $suspiciousUser = mysqli_query($conn, $sql);
            if(mysqli_num_rows($suspiciousUser) > 0)
            {
                $locked_until = lockFiveMinutes();
                $sql = "UPDATE MyGuests SET locked_until = '$locked_until' WHERE user='$username'";
                mysqli_query($conn, $sql);
                echo "<script>
                    alert('Username or password incorrect! Your account is blocked for 5 minutes'); 
                    window.location.href='index.php';
                </script>";
            }
            else
            {
                //TODO: work here, does not work!!!
                $ip = $_SESSION['ip'];
                $userAgent = $_SESSION['clientAgent'];
                $locked_until = lockFiveMinutes();
                $sql = "INSERT INTO NoGuests(ip, clientAgent, locked_until) VALUES ('$ip', '$userAgent', '$locked_until')";
                if(mysqli_query($conn, $sql))
                {
                    echo "<script>alert('Website blocked!')</script>";
                    header('Refresh:0 url=index.php');
                } 
                else 
                {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                echo "<script>
                    alert('Username or password incorrect!'); 
                    window.location.href='index.php';
                </script>";
            }
        }
        else
        {
            echo "<script>
                    window.location.href='index.php';
                </script>";
        }
    }

    function logIn($username, $pass, $conn)
    {
        $SQL = "SELECT user FROM MyGuests WHERE user='$username' AND passwd='$pass'";
        $login_user = mysqli_query($conn, $SQL);
        if(mysqli_num_rows($login_user) > 0)
        {
            $ip = $_SESSION['ip'];
            $userAgent = $_SESSION['clientAgent'];
            session_unset();
            session_destroy();
            $sql = "UPDATE MyGuests SET login_date = CURRENT_TIMESTAMP, ip = '$ip', clientAgent = '$userAgent' WHERE user='$username' AND passwd='$pass'";
            $result = mysqli_query($conn, $sql);
            session_id(generateRandomSessionID());
            session_start(); 
            $_SESSION["id_s"] = session_id();
            $_SESSION["name"] = $username;        
            header('Location: profile.php');
            $_SESSION['login_attempts'] = 0;
        }
        else
        {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($login_user);
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