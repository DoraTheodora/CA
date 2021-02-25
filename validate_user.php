<?php
    require_once 'security_methods.php';
    session_start();
    if($_SESSION['lockedTime'] > time())
	{
		header("Location: blocked.php");
	}
    if(isset($_POST['username']) && isset($_POST['pass']) && isset($_SESSION['login_attempts']) && isset($_SESSION['ip']) && isset($_SESSION['clientAgent']) && isset($_SESSION['blocked']) && isset($_SESSION['incorrect_credentials']) && isset($_SESSION['invalid_captcha']) && isset($_SESSION['lockedTime']))
    {
        
        $username = filter($_POST['username']);
        $password = filter($_POST['pass']);
        if($username != $_POST['username'] || $password != $_POST['pass'])
        {
            $_SESSION['illegal_characters'] = true;
            header('Refresh:0');
        }

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
                $sql = "SELECT * FROM NoGuests WHERE ip=? AND clientAgent=?";
                $query = $conn->prepare($sql);
                $query->bind_param("ss",$ip, $agent);
                $query->execute();
                //$suspiciousAgent = mysqli_query($conn, $sql);
                //$rows = mysqli_num_rows($suspiciousAgent);
                $results = $query->get_result()->fetch_assoc();
                if(!empty($results))
                {
                    $start_date_time = date("Y-m-d H:i:s");
                    $locked_until = date('Y-m-d H:i:s',strtotime('+3 minutes',strtotime($start_date_time)));
                    
                    $sql = "UPDATE NoGuests SET locked_until = ? WHERE ip=? AND clientAgent=?";
                    $query = $conn->prepare($sql);
                    $query->bind_param("sss", $locked_until, $ip, $agent);
                    if(!$query->execute())
                    {
                        "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
                    }
                    else
                    {
                        log_activity("failed auth more than 5 times", $ip, $agent, "blocked");
                    }  
                    $query->close();
                }
                else 
                {
                    $start_date_time = date("Y-m-d H:i:s");
                    $locked_until = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($start_date_time)));
                    $sql = "INSERT INTO NoGuests(ip, clientAgent, locked_until) VALUES (?, ?, ?)";
                    $query = $conn->prepare($sql);
                    $query->bind_param("sss",$ip, $agent, $locked_until);
                    if(!$query->execute()) 
                    {
                        "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
                    } 
                    else
                    {
                        log_activity("failed auth more than 5 times", $ip, $agent, "blocked");
                    }
                    $query->close();
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
        $sql = "SELECT MAX(locked_until) FROM NoGuests WHERE ip=? AND clientAgent=?";
        $query = $conn->prepare($sql);
        $query->bind_param("ss",$ip, $agent);
        $query->execute();
        $suspiciousUser = $query->get_result()->fetch_array();
        $query->close();
        $locked_until = strtotime($suspiciousUser[0]);
        $blocked_time = $locked_until-time();
        /*echo $sql;
        echo "<br>Blocked until:".$locked_until;
        echo "<br>Current time: ".time();
        echo "<br>Blocked time: ".$blocked_time;*/
        $_SESSION['lockedTime'] = $locked_until;
        if(!empty($suspiciousUser))
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
            $sql = "SELECT salt, passwd FROM MyGuests WHERE user=?";
            $query = $conn->prepare($sql);
            $query->bind_param("s", $username);
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            $ip = $_SESSION['ip'];
            $agent = $_SESSION['clientAgent'];
            if(!empty($result))
            {
                $salt = $result['salt'];
                $pass = $result['passwd'];
                $to_hash = $password . $salt;
                if(password_verify($to_hash, $pass))
                {
                    logIn($username, $pass, $conn);
                }
                else
                {
                    log_activity("authentication", $ip, $agent, "invalid credentials");
                    $_SESSION['incorrect_credentials'] = true;
                    header('Refresh:0');
                }
            }
            else
            {
                log_activity("authentication", $ip, $agent, "invalid credentials");
                $_SESSION['incorrect_credentials'] = true;
                header('Refresh:0');
            }
            $query->close();
        }
        else
        {
            $_SESSION['blocked'] = true;
            header("Location: blocked.php");
        }
    }

    function logIn($username, $pass, $conn)
    {
        $SQL = "SELECT user FROM MyGuests WHERE user=? AND passwd=?";
        $query = $conn->prepare($SQL);
        $query->bind_param("ss",$username, $pass);
        $users = $query->execute();
        //$login_user = mysqli_query($conn, $SQL);
        $results = $query->get_result()->fetch_assoc();
        if(!empty($results))
        {
            $ip = $_SESSION['ip'];
            $userAgent = $_SESSION['clientAgent'];
            session_unset();
            session_destroy();
            $now = date("Y-m-d H:i:s");
            $sql = "UPDATE MyGuests SET login_date = ?, ip = ?, clientAgent = ? WHERE user=? AND passwd=?";
            $query = $conn->prepare($sql);
            $query->bind_param("sssss",$now, $ip, $userAgent, $username, $pass);
            if($query->execute())
            {
                log_activity("authentication", $ip, $userAgent, "approved");
                //$result = mysqli_query($conn, $sql);
                session_id(generateRandomSessionID());
                session_start(); 
                $_SESSION["id_s"] = session_id();
                $_SESSION["name"] = $username;    
                if(is_admin($username))
                {
                    $_SESSION["is_admin"] = true;
                }
                else
                {
                    $_SESSION["is_admin"] = false;
                }
                header('Location: profile.php');
                $_SESSION['login_attempts'] = 0;
            }
            else
            {
                log_activity("authentication", $ip, $userAgent, "server error");
            }
            $query->close();
            
        }
        else
        {
            $_SESSION['incorrect_credentials'] = true;
            header('Refresh:0');
            echo $query->error;
        }  
    }

    function is_admin($username)
    {
        include 'conf.php';
        $sql = "SELECT isAdmin FROM MyGuests WHERE user=?";
        $query = $conn->prepare($sql);
        $query->bind_param("s",$username);
        if(!$query->execute()) 
        {
            "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
            return false;
        }
        $results = $query->get_result()->fetch_assoc();
        if(!empty($results))
        {
            if($results['isAdmin'] == 1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
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