<?php
    require 'conf.php';
    require 'security_methods.php';
    session_start();
    $create_user = true;
    if(isset($_POST['sign_up_username']) && isset($_POST['pass1']) && isset($_POST['pass2']))
    {
        $username = filter($_POST['sign_up_username']);
        $password2 = filter($_POST['pass2']);
        $password1 = filter($_POST['pass1']);
        if($username != $_POST['sign_up_username'] || $password1 != $_POST['pass1'] ||$password2 != $_POST['pass2'])
        {
            log_activity("filter username and password", $ip, $agent, "illegal characters found");
            $create_user = false;
            $_SESSION['illegal_characters'] = true;
            header("Refresh:0");
        }
        if (!$conn) 
        {
            die("Connection failed: " . mysqli_connect_error());
        }
        if(userExistsInTheDatabase($username, $conn))
        {
            $create_user = false;
            $_SESSION['username_exists'] = true;
            header("Refresh:0");
        }
        else if(!passwords_matching($password1, $password2))
        {
            $create_user = false;
            $_SESSION['passwords_not_matching'] = true;
            header("Refresh:0");
        }
        else if(!password_length($password1))
        {
            $create_user = false;
            $_SESSION['password_too_short'] = true;
            header("Refresh:0");
        }
        else if(!password_has_all_required_characters($password1))
        {
            $create_user = false;
            $_SESSION['password_needs_other_type_of_characters'] = true;
            header("Refresh:0");
        }
        else if(username_is_in_password($password1, $username))
        {
            $create_user = false;
            $_SESSION['username_in_password'] = true;
            header("Refresh:0");
        }
        if($create_user)
        {
            createAccount($username, $password1, $conn);
        }
    }
    else
    {
        header("Location: signup.html.php");
    }   
?>  

