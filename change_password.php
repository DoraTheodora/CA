<?php
    require 'conf.php';
    require 'security_methods.php';
    session_start();
    if(isset($_SESSION["id_s"]) && session_id() == $_SESSION["id_s"])
	{
		echo $_SESSION["id_s"];
        $change_password = true;
        if(isset($_GET['existing_password']) && isset($_GET['pass1']) && isset($_GET['pass2']))
        {

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