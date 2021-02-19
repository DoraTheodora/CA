<?php
    session_start();
    if(isset($_POST['username']) && isset($_POST['pass']) && isset($_SESSION['login_attempts']) && isset($_SESSION['ip']) && isset($_SESSION['clientAgent']) && isset($_SESSION['blocked']) && isset($_SESSION['incorrect_credentials']) && isset($_SESSION['invalid_captcha']))
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
    function check_if_user_is_locked($username, $password)
    {
        // ! user has an account
        include 'conf.php';
        $sql = "SELECT locked_until FROM MyGuests WHERE user='$username'";
        $suspiciousUser = mysqli_query($conn, $sql);
        $start_date_time = date("Y-m-d H:i:s");
        if(mysqli_num_rows($suspiciousUser) > 0)
        {
            $details = mysqli_fetch_assoc($suspiciousUser);
            if($details['locked_until'] >  $start_date_time)
            {
                $time_left = $details['locked_until'] - $start_date_time;
                $_SESSION['blocked'] = true;
                header('Refresh:0 url=index.php');
            }
            else
            {
                validateUser($username, $password);
            }
        }
        // ! user does not have an account
        else
        {
            $ip = $_SESSION['ip'];
            $agent = $_SESSION['clientAgent'];
            $sql = "SELECT * FROM NoGuests WHERE ip='ip' AND clientAgent='$agent'";
            $suspiciousUser = mysqli_query($conn, $sql);
            $start_date_time = date("Y-m-d H:i:s");
            if(mysqli_num_rows($suspiciousUser) > 0)
            {
                $details = mysqli_fetch_assoc($suspiciousUser);
                if($details['locked_until'] >  $start_date_time)
                {
                    $time_left = $details['locked_until'] - $start_date_time;
                    $_SESSION['blocked'] = true;
                    header('Refresh:0 url=index.php');
                }
            }
            else
            {
                $_SESSION['login_attempts']++;
                $_SESSION['incorrect_credentials'] = true;
                header('Refresh:0 url=index.php');
            }
        }
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
                if($_SESSION['login_attempts'] >= 3)
                {
                    if ($_POST["vercode"] != $_SESSION["vercode"] OR $_SESSION["vercode"]=='')  
                    {
                        $_SESSION['invalid_captcha'] = true;
                        header('Refresh:0 url=index.php');
                    } 
                    else
                    {
                        logIn($username, $pass, $conn);
                    }
                }
                else if($_SESSION['login_attempts'] < 3)
                {
                    logIn($username, $pass, $conn);
                }
            }  
            else
            {
                $_SESSION['incorrect_credentials'] = true;
                header('Refresh:0 url=index.php');

                if($_SESSION['login_attempts']%5 == 0)
                {
                    $sql = "SELECT locked_until FROM MyGuests WHERE user='$username'";
                    $suspiciousUser = mysqli_query($conn, $sql);
                    //! if the user that attempts to log in has an account
                    if(mysqli_num_rows($suspiciousUser) > 0)
                    {
                        $start_date_time = date("Y-m-d H:i:s");
                        $locked_until = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($start_date_time)));
                        $sql = "UPDATE MyGuests SET locked_until = '$locked_until' WHERE user='$username'";
                        mysqli_query($conn, $sql);
                    }
                    //! is the user that attempts to log in does not have an account
                    else if(mysqli_num_rows($suspiciousUser) == 0)
                    {
                        
                        $ip = $_SESSION['ip'];
                        $agent = $_SESSION['clientAgent'];
                        $sql = "SELECT * FROM NoGuests WHERE ip='ip' AND clientAgent='$agent'";
                        $suspiciousAgent = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($suspiciousAgent) > 0)
                        {

                        }
                        else
                        {
                            echo "locking";
                            
                            $start_date_time = date("Y-m-d H:i:s");
                            $locked_until = date('Y-m-d H:i:s',strtotime('+5 minutes',strtotime($start_date_time)));
                            $sql = "INSERT INTO NoGuests(ip, clientAgent, locked_until) VALUES ('$ip', '$agent', $locked_until)";
                            if(mysqli_query($conn, $sql))
                            {
                                $_SESSION['blocked'] = true;
                                header('Refresh:0 url=index.php');
                            } 
                            else 
                            {
                                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                            } 
                        } 
                    }
                }
                else
                {
                    echo "<script>
                            window.location.href='index.php';
                        </script>";
                }
            }  
        }
        else
        {
            $_SESSION['incorrect_credentials'] = true;
            header('Refresh:0 url=index.php');
        }
    }

    function block()
    {
        
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