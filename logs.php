<?php
    header("Content-Security-Policy: frame-ancestors 'none'", false);
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    session_cache_limiter('nocache');


    session_start();
    require 'security_methods.php';
    check_session_id();
    if(isset($_SESSION["id_s"]) && session_id() == $_SESSION["id_s"])
	{
        $idle_time = time() - $_SESSION['time_user_logged_in'];
		echo "<br>Idle time: ".$idle_time."s";
		$max_time_allowed = time() - $_SESSION['max_session_duration'];
		echo "<br>Max Time Allowed: ". $max_time_allowed."s";
		if(time() - $_SESSION['time_user_logged_in'] > $_SESSION['max_idle_duration'])
		{
			header("Location: logout.php");
		}
		else
		{
			if(time() >= $_SESSION['max_session_duration'])
			{
				header("Location: logout.php");
			}
			else
			{
				$_SESSION['time_user_logged_in'] = time();	
                echo'
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <title>Theodora Tataru</title>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <!--===============================================================================================-->
                        <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
                        <link rel="stylesheet" type="text/css" href="css/util.css">
                        <link rel="stylesheet" type="text/css" href="css/main.css">
                        <!--===============================================================================================-->
                    </head>
                    <body>
                        <form action>
                            <div class="limiter">
                                <span class="author p-b-49" > Theodora Tataru, C00231174 <br> 2021 </span> 
                                <div>';
                                    if($_SESSION['is_admin']==false)
                                    {
                                        echo '<span class="login100-form-title p-b-49">
                                        We are sorry, you are not authorized to see this page
                                        </span>';
                                    }
                                    else
                                    {
                                        get_log();
                                    }
                                    echo '
                                    <span class="login100-form-title p-b-20">
                                            <span class="txt1 p-b-2">
                                                <a href="profile.php" class="txt2">
                                                    Profile
                                                </a>
                                            </span><br>
                                            <span class="txt1 p-b-2">
													<a href="change_password.html.php" class="txt2">
														Change Password
													</a>
                                            </span><br>
                                            <span class="txt1 p-b-2">
                                                <a href="php_info.php" class="txt2">
                                                    PHP Info
                                                </a>
                                            </span><br>
                                            <span class="txt1 p-b-2">
                                                <a href="turtle.php" class="txt2">
                                                    Pretty Turtle
                                                </a>
                                            </span><br>
                                            <span class="txt1 p-b-2">
                                                <a href="logout.php" class="txt2">
                                                    Log out
                                                </a>
                                            </span><br><br><br><br>
                                        </span>
                                </div>
                            </div>
                        </form>

                        <div id="dropDownSelect1"></div>
                    </body>
                    </html>';
                                }

            }
        }
    else
    {
        header("Location: login.html.php");
    }
    ?>
