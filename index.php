<?php
	session_start();
	if(!isset($_SESSION['login_attempts']))
	{
		$_SESSION['login_attempts'] = 0;
	}
	if(!isset($_SESSION['ip']))
	{
		$_SESSION['ip'] = getIPAddress();
	}
	if(!isset($_SESSION['clientAgent']))
	{
		$_SESSION['clientAgent'] = getClientAgent();
	}


	function getClientAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    function getIPAddress()
    {
        //whether ip is from the share internet  
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }  
        //whether ip is from the remote address  
        else
        {  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }
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
		<div class="container-login100" style="background-image: url('images/bg-01.jpg');">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login100-form validate-form" action="validate_user.php" method="POST">
					<span class="login100-form-title p-b-49">
						Login
					</span>

					<div class="wrap-input100 validate-input m-b-23" data-validate = "Username is required">
						<span class="label-input100">Username</span>
						<input class="input100" type="text" name="username" placeholder="Type your username">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<span class="label-input100">Password</span>
						<input class="input100" type="password" name="pass" placeholder="Type your password">
						<span class="focus-input100" data-symbol="&#xf190;"></span>
					</div>
					<?php
						if($_SESSION['login_attempts'] >= 3)
						{
							echo '
								<div class="wrap-input100 validate-input" data-validate="Captcha is required">
									<span class="label-input100">Password</span>
									<input class="input100" type="text" name="vercode" placeholder="Type the verification code" required="required">
									<span class="focus-input100" data-symbol="&#xf190;"></span>
								</div> 
								<div class="form-group small clearfix">
									<span class="label-input100">Captcha</span>
										<label class="checkbox-inline">Verification Code</label>
										&nbsp;&nbsp;<img src="captcha.php" >
									<span class="focus-input100" data-symbol="&#xf190;"></span>
        						</div>';
						}
					?>
					
					<div class="text-right p-t-8 p-b-31">
						<a href="#">
							Forgot password?
						</a>
					</div>
					
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" type="submit">
								Login
							</button>
						</div>
					</div>

					<div class="flex-col-c p-t-30">
						<span class="txt1 p-b-2">
                            <a href="signup.html" class="txt2">
                                Sign Up
                            </a>
						</span>
                        <a href="create_database.php" class="txt2 p-t-100">
							Create Database
						</a>
						<br>
						<?php echo "Login attempts ". $_SESSION['login_attempts']; ?>
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
</html>