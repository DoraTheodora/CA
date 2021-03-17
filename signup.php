<?php
    require 'conf.php';
    require 'security_methods.php';
    header("Content-Security-Policy: frame-ancestors 'none'", false);
	header('X-Frame-Options: SAMEORIGIN');
	header('X-XSS-Protection: 1; mode=block');
	header('X-Frame-Options: DENY');
	header('X-Content-Type-Options: nosniff');
	session_cache_limiter('nocache');

    session_start();
    $create_user = true;
    if($_SESSION['signup_attempts'] >= 5)
    {
        lock_user();
    }
    else
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST))
        {
            if(isset($_POST['sign_up_username']) && isset($_POST['pass1']) && isset($_POST['pass2']))
            {
                $username = filter($_POST['sign_up_username']);
                $password2 = filter($_POST['pass2']);
                $password1 = filter($_POST['pass1']);
                $ip = getIPAddress();
                $agent = getClientAgent();
                 if (!$conn) 
                {
                    die("Connection failed: " . mysqli_connect_error());
                }
                else if(userExistsInTheDatabase($username, $conn))
                {
                    $create_user = false;
                    log_activity("Sign up", $ip, $agent, "User exists in the database");
                    $_SESSION['username_exists'] = true;
                }
                else if(!passwords_matching($password1, $password2))
                {
                    $create_user = false;
                    log_activity("Sign up", $ip, $agent, "Invalid credentials");
                    $_SESSION['passwords_not_matching'] = true;
                }
                else if(!password_length($password1))
                {
                    $create_user = false;
                    log_activity("Sign up", $ip, $agent, "Password does not obey the password complexity rules");
                    $_SESSION['password_too_short'] = true;
                }
                else if(!password_has_all_required_characters($password1))
                {
                    $create_user = false;
                    log_activity("Sign up", $ip, $agent, "Password does not obey the password complexity rules");
                    $_SESSION['password_needs_other_type_of_characters'] = true;
                }
                else if(username_is_in_password($password1, $username))
                {
                    $create_user = false;
                    log_activity("Sign up", $ip, $agent, "Password does not obey the password complexity rules");
                    $_SESSION['username_in_password'] = true;
                }
                if($create_user)
                {
                    createAccount($username, $password1, $conn);
                }
                else
                {
                    $_SESSION['signup_attempts']++;
                    header("Refresh:0");
                }
            }
            else
            {
                header("Location: signup.html.php");
            }   
        }
        else
        {
            header("Location: signup.html.php");
        }
    }   

?>  

