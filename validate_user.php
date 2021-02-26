<?php
    header("Content-Security-Policy: frame-ancestors 'none'", false);
    header('X-Frame-Options: SAMEORIGIN');
    require 'security_methods.php';
    
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
            if($_SESSION['invalid_captcha'] == false)
            {
                validateUser($username, $password);
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
?>