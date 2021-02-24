<?php
    session_start();
    if($_SESSION['lockedTime'] > time())
	{
		header("Location: blocked.php");
	}
    if(isset($_POST['username']) && isset($_POST['pass']) && isset($_SESSION['login_attempts']) && isset($_SESSION['ip']) && isset($_SESSION['clientAgent']) && isset($_SESSION['blocked']) && isset($_SESSION['incorrect_credentials']) && isset($_SESSION['invalid_captcha']) && isset($_SESSION['lockedTime']))
    {
        $username = $_POST['username'];
        $password = $_POST['pass'];
        $_SESSION['login_attempts']++;
        if($_SESSION['login_attempts'] <= 3)
        {
            validateUser($username, $password);
        }
        else if($_SESSION['login_attempts'] >= 4 && $_SESSION['login_attempts'] < 5)
        {
            if ($_POST["vercode"] != $_SESSION["vercode"] OR $_SESSION["vercode"]=='')  
            {
                $_SESSION['invalid_captcha'] = true;
                header('Refresh:0');
            } 
            else
            {
                validateUser($username, $password);
            }
        }
        else if($_SESSION['login_attempts'] >= 5)
        {
            if(($_POST["vercode"] != $_SESSION["vercode"] || $_SESSION["vercode"]==''))
            {
                $_SESSION['invalid_captcha'] = true;
                header('Refresh:0');
            }
            else
            {
                include 'conf.php';
                $ip = $_SESSION['ip'];
                $agent = $_SESSION['clientAgent'];
                $sql = "SELECT * FROM NoGuests WHERE ip='$ip' AND clientAgent='$agent'";
                $suspiciousAgent = mysqli_query($conn, $sql);
                $rows = mysqli_num_rows($suspiciousAgent);
                if($rows > 0)
                {
                    $start_date_time = date("Y-m-d H:i:s");
                    $locked_until = date('Y-m-d H:i:s',strtotime('+3 minutes',strtotime($start_date_time)));
                    
                    $sql = "UPDATE NoGuests SET locked_until = '$locked_until' WHERE ip='$ip' AND clientAgent='$agent'";
                    if(!mysqli_query($conn, $sql))
                    {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    }    
                }
                else 
                {
                    $start_date_time = date("Y-m-d H:i:s");
                    $locked_until = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($start_date_time)));
                    $sql = "INSERT INTO NoGuests(ip, clientAgent, locked_until) VALUES ('$ip', '$agent', '$locked_until')";
                    if(mysqli_query($conn, $sql))
                    {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    } 
                }
                if(is_user_locked()) 
                {
                    header("Location: blocked.php");
                }
            }
        }
    }
    else
    {
        header('Location: index.php');
    }
    // * Functions ---------------------------------------------------------------------------------------------------------
    function is_user_locked()
    {
        //? This method checks is the device the users uses to log in is locked or not
        include 'conf.php';
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];
        $sql = "SELECT MAX(locked_until) FROM NoGuests WHERE ip='$ip' AND clientAgent='$agent'";
        $suspiciousUser = mysqli_query($conn, $sql);
        $time_blocked = mysqli_fetch_array($suspiciousUser);
        $locked_until = strtotime($time_blocked[0]);
        $blocked_time = $locked_until-time();
        /*echo $sql;
        echo "<br>Blocked until:".$locked_until;
        echo "<br>Current time: ".time();
        echo "<br>Blocked time: ".$blocked_time;*/
        $_SESSION['lockedTime'] = $locked_until;
        if($suspiciousUser->num_rows > 0)
        {
            if($blocked_time > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function validateUser($username, $password)
    {
        //? This method checks the user's credentials
        include 'conf.php';
        if(!is_user_locked())
        {
            $sql = "SELECT salt, passwd FROM MyGuests WHERE user='$username'";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $details = mysqli_fetch_assoc($result);  
                $salt = $details['salt'];
                $pass = $details['passwd'];
                $to_hash = $password . $salt;
                if(password_verify($to_hash, $pass))
                {
                    logIn($username, $pass, $conn);
                }
                else
                {
                    $_SESSION['incorrect_credentials'] = true;
                    header('Refresh:0');
                }
            }
            else
            {
                $_SESSION['incorrect_credentials'] = true;
                header('Refresh:0');
            }
        }
        else
        {
            $_SESSION['blocked'] = true;
            header("Location: blocked.php");
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
            $now = date("Y-m-d H:i:s");
            $sql = "UPDATE MyGuests SET login_date = '$now', ip = '$ip', clientAgent = '$userAgent' WHERE user='$username' AND passwd='$pass'";
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
            $_SESSION['incorrect_credentials'] = true;
            header('Refresh:0');
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