<?php
    session_start();
    include 'conf.php';
    include 'security_methods.php';
    if(isset($_SESSION["id_s"]) && session_id() == $_SESSION["id_s"])
	{
		echo $_SESSION["id_s"];
        $change_password = true;
        if(isset($_GET['existing_password']) && isset($_GET['pass1']) && isset($_GET['pass2']))
        {
            $existing_pass = filter($_GET['existing_password']);
            $password2 = filter($_GET['pass2']);
            $password1 = filter($_GET['pass1']);
            if($existing_pass != $_GET['existing_password'] || $password1 != $_GET['pass1'] ||$password2 != $_GET['pass2'])
            {
                $agent = getClientAgent();
                $ip = getIPAddress();
                log_activity("filter username and password", $ip, $agent, "illegal characters found");
                $change_password = false;
                $_SESSION['illegal_characters'] = true;
                header("Location: change_password.html.php");
            }
            if (!$conn) 
            {
                die("Connection failed: " . mysqli_connect_error());
            }
            else if(!check_existing_password($_SESSION['name'], $existing_pass))
            {
                $change_password = false;
                $_SESSION['invalid_password'] = true;
                //TODO: do not allow the user to go back
                header("Location: change_password.html.php");
            }
            else if(!passwords_matching($password1, $password2))
            {
                $change_password = false;
                $_SESSION['passwords_not_matching'] = true;
                header("Location: change_password.html.php");
            }
            else if(new_pass_is_the_same_as_old_pass($password1, $existing_pass))
            {
                $change_password = false;
                $_SESSION['new_pass_equals_old_pass'] = true;
                header("Location: change_password.html.php");
            }
            else if(!password_length($password1))
            {
                $change_password = false;
                $_SESSION['password_too_short'] = true;
                header("Location: change_password.html.php");
            }
            else if(!password_has_all_required_characters($password1))
            {
                $change_password = false;
                $_SESSION['password_needs_other_type_of_characters'] = true;
                header("Location: change_password.html.php");
            }
            else if(username_is_in_password($password1, $_SESSION['name']))
            {
                $change_password = false;
                $_SESSION['username_in_password'] = true;;
                header("Location: change_password.html.php");
            }
            if($change_password)
            {
                change_password($_SESSION['name'], $password1);
            }
        }
        else
        {
            header("Location: change_password.html.php");
        }  
    }
    else
    {
        echo "<script>
				alert('You are not logged in. Access denied'); 
				window.location.href='index.php';
			</script>";
    }
?>


<?php

    function change_password($username, $password)
    {
        include 'conf.php';
        $salt = generateSalt();
        $to_hash = $password . $salt;
        $hash_pass = password_hash($to_hash, PASSWORD_ARGON2I);
        $agent = getClientAgent();
        $ip = getIPAddress();
        $isAdmin = 0;

        $sql = "UPDATE MyGuests SET passwd = ?, salt = ? WHERE user = ? AND ip = ? AND clientAgent = ?";
        $query = $conn->prepare($sql);
        $query->bind_param("sssss",$hash_pass, $salt, $username, $ip, $agent);
        if($query->execute())
        {
            log_activity("change password", $ip, $agent, "password changed");
            echo "<script>alert('Password change!!')</script>";
            session_unset();
            session_destroy();
            header('Refresh:0 url=index.php');
        }
        else
        {
            log_activity("change password", $ip, $agent, "password not changed");
            echo $query->error;
        }
        $query->close();
    }

    function check_existing_password($username, $password)
    {
        include 'conf.php';
        $sql = "SELECT salt, passwd FROM MyGuests WHERE user=?";
        $query = $conn->prepare($sql);
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        if(!empty($result))
        {
            $salt = $result['salt'];
            $pass = $result['passwd'];
            $to_hash = $password . $salt;
            if(password_verify($to_hash, $pass))
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

    function new_pass_is_the_same_as_old_pass($new_pass, $old_pass)
    {
        return $new_pass == $old_pass;
    }

?>