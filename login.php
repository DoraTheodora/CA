<?php
    header("Content-Security-Policy: frame-ancestors 'none'", false);
	header('X-Frame-Options: SAMEORIGIN');
	header('X-XSS-Protection: 1; mode=block');
	header('X-Frame-Options: DENY');
	header('X-Content-Type-Options: nosniff');
	session_cache_limiter('nocache');
    require 'security_methods.php';

    session_start();
    is_user_locked();
    if($_SESSION['lockedTime'] > time())
    {
        header("Location: blocked.php");
    }
    $_SESSION['login_attempts']++;
    if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST))
    {
        if(isset($_POST['username']) && isset($_POST['pass']) && isset($_SESSION['login_attempts']) && isset($_SESSION['ip']) && isset($_SESSION['clientAgent']) && isset($_SESSION['blocked']) && isset($_SESSION['incorrect_credentials']) && isset($_SESSION['invalid_captcha']) && isset($_SESSION['lockedTime']))
        {
            $username = filter($_POST['username']);
            $password = filter($_POST['pass']);
            $_SESSION['username'] = $username;

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
                if(($_POST["vercode"] == $_SESSION["vercode"] && $_SESSION["vercode"]!=''))
                {
                    validateUser($username, $password);
                }
                else
                {
                    lock_user();
                }
            }
        }
        else
        {
            header('Location: login.html.php');
        }
    }
    else
    {
        header('Location: login.html.php');
    }
?>