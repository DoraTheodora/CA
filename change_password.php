<?php
    include 'conf.php';
    include 'security_methods.php';
    session_start();
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
                //header("Refresh:0");
            }
            if (!$conn) 
            {
                die("Connection failed: " . mysqli_connect_error());
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

    function check_existing_password($username)
    {
        
    }

?>