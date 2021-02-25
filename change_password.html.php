<?php
	session_start();
    if(!isset($_SESSION['invalid_password']))
    {
        $_SESSION['invalid_password'] = false;
    }
    if(!isset($_SESSION['passwords_not_matching']))
    {
        $_SESSION['passwords_not_matching'] = false;
    }
    if(!isset($_SESSION['password_too_short']))
    {
        $_SESSION['password_too_short'] = false;
    }
    if(!isset($_SESSION['password_needs_other_type_of_characters']))
    {
        $_SESSION['password_needs_other_type_of_characters'] = false;
    }
    if(!isset($_SESSION['username_in_password']))
    {
        $_SESSION['username_in_password'] = false;
    }
    if(!isset($_SESSION['illegal_characters']))
    {
        $_SESSION['illegal_characters'] = false;
    }
    if(!isset($_SESSION['new_pass_equals_old_pass']))
    {
        $_SESSION['new_pass_equals_old_pass'] = false;
    }
    
	if(isset($_SESSION["id_s"]) && session_id() == $_SESSION["id_s"])
	{
		echo $_SESSION["id_s"];
?>
        <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Theodora Tataru</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->	
        <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="css/util.css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
    </head>
    <body>
        <div class="limiter">
            <span class="author p-b-1" > Theodora Tataru, C00231174</span> <br>
            <span class="author p-b-1" > 2021</span>
            <div class="container-login100">
                <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
                    <form class="login100-form validate-form" action="change_password.php" method="GET">
                        <div class="info">
                            To change your password, you need to:
                            <ul>
                                <li> &nbsp;&nbsp;&nbsp;&nbsp; Know your old password </li>
                                <li> &nbsp;&nbsp;&nbsp;&nbsp; The new password needs to have: </li>
                                <ol>
                                    <li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; has minimum 8 characters </li>
                                    <li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; has minimum 1 number </li>
                                    <li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; has minimum 1 uppercase letter </li>
                                    <li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; has minimum 1 lowercase letter </li>
                                    <li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; has minimum 1 symbol </li>
                                </ol>
                            </ul>
                        </div>
                        <br><br>
                        <span class="login100-form-title p-b-49">
                            Change Password
                        </span>
                        <?php
                            if($_SESSION['invalid_password'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p> Invalid current password </p></span>";
                                $_SESSION['invalid_password'] = false;
                            }
                            if($_SESSION['passwords_not_matching'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p> Passwords do not match! </p> </span>";
                                $_SESSION['passwords_not_matching'] = false;
                            }
                            if($_SESSION['password_too_short'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p> Password is too short!\nThe password should have at least 8 characters </p> </span>";
                                $_SESSION['password_too_short'] = false;
                            }
                            if($_SESSION['password_needs_other_type_of_characters'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p> The password should have: minimum:</p> <p> &nbsp;&nbsp;&nbsp;&nbsp; 8 characters, 1 number, 1 uppercase letter, 1 lowercase letter and 1 symbol </p> </span>";
                                $_SESSION['password_needs_other_type_of_characters'] = false;
                            }
                            if($_SESSION['username_in_password'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p> The password cannot contain the username in it\nPlease try again </p> </span>";
                                $_SESSION['username_in_password'] = false;
                            }
                            if($_SESSION['illegal_characters'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p>Illegal characters were used in your username or password.</p>
                                    <p>The following characters are not allowed:</p>
                                    <p>&nbsp;&nbsp;&nbsp;&nbsp; &amp  &lt  &gt  &#40  &#41  &#123  &#125
                                    &#91 &#93  &#34  &#39  &#59  &#47  &#92 </p>  </span>";
                                $_SESSION['illegal_characters'] = false;
                            }
                            if($_SESSION['new_pass_equals_old_pass'])
                            {
                                echo "
                                <span class='error p-b-5'> <p> The new password must be different from old password\nPlease try again </p> </span>";
                                $_SESSION['new_pass_equals_old_pass'] = false;
                            }
                        ?>
                        <br>
                        <div class="wrap-input100 validate-input m-b-23" data-validate = "Password is required">
                            <span class="label-input100">Existing password</span>
                            <input class="input100" type="password" name="existing_password" placeholder="Type your existing password" required>
                            <span class="focus-input100" data-symbol="&#xf190;"></span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="Password is required">
                            <span class="label-input100">New Password</span>
                            <input class="input100" type="password" name="pass1" placeholder="Type your new password" required>
                            <span class="focus-input100" data-symbol="&#xf190;"></span>
                        </div>
                        
                        <div class="wrap-input100 validate-input" data-validate="Password is required">
                            <input class="input100" type="password" name="pass2" placeholder="Re-enter your new password" required>
                            <span class="focus-input100" data-symbol="&#xf190;"></span>
                        </div>
                        <div class="container-login100-form-btn">
                            <div class="wrap-login100-form-btn">
                                <div class="login100-form-bgbtn"></div>
                                <button class="login100-form-btn" type="submit" >
                                    Submit
                                </button>
                            </div>
                        </div>
                        
                        <div class="container-login100-form-btn">
                            <div class="flex-col-c p-t-30">
								<?php
									if($_SESSION['is_admin']==true)
									{
										echo '<span class="txt1 p-b-2">
												<a href="logs.php" class="txt2">
													Logs
												</a>
											</span>';

									}
								?>
                                <span class="txt1 p-b-2">
									<a href="profile.php" class="txt2">
                                        Profile
									</a>
								</span>
								<span class="txt1 p-b-2">
									<a href="turtle.php" class="txt2">
										Pretty Turtle
									</a>
								</span>
								<span class="txt1 p-b-2">
									<a href="logout.php" class="txt2">
										Log out
									</a>
								</span>
							</div>
                    </form>
                </div>
            </div>
        </div>
        

        <div id="dropDownSelect1"></div>
        
    <!--===============================================================================================-->
        <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
        <script src="vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
        <script src="vendor/bootstrap/js/popper.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
        <script src="vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
        <script src="vendor/daterangepicker/moment.min.js"></script>
        <script src="vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
        <script src="vendor/countdowntime/countdowntime.js"></script>
    <!--===============================================================================================-->
        <script src="js/main.js"></script>

    </body>


<?php
	}
	else
	{
		echo "<script>
				alert('You are not logged in. Access denied'); 
				window.location.href='index.php';
			</script>";
	}
?>
</html>