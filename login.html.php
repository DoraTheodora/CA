<?php
	//header("Content-Security-Policy: frame-ancestors 'none'", false);
	//header('X-Frame-Options: SAMEORIGIN');
	require 'security_methods.php';
	session_start();
	session_regenerate_id();
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
	if(!isset($_SESSION['blocked']))
	{
		$_SESSION['blocked'] = false;
	}
	if(!isset($_SESSION['incorrect_credentials']))
	{
		$_SESSION['incorrect_credentials'] = false;
	}
	if(!isset($_SESSION['invalid_captcha']))
	{
		$_SESSION['invalid_captcha'] = false;
	}
	if(!isset($_SESSION['lockedTime']))
	{
		$_SESSION['lockedTime'] = time();
	}
	if(!isset($_SESSION['is_admin']))
	{
		$_SESSION['is_admin'] = false;
	}
	if(!isset($_SESSION['illegal_characters']))
	{
		$_SESSION['illegal_characters'] = false;
	}	
?>

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
	<div class="limiter">
        <span class="author p-b-1" > Theodora Tataru, C00231174</span> <br>
        <span class="author p-b-1" > 2021</span>
		<div class="container-login100" style="background-image: url('images/bg-01.jpg');">
			<div class="wrap-login100 p-l-30 p-r-30 p-t-65 p-b-54">
				<form class="login100-form validate-form" method='POST' action='login.php'>
					<span class="login100-form-title p-b-49">
						Login
					</span>
					<?php
					if($_SESSION['incorrect_credentials'])
					{
						echo "
							<span class='error p-b-5'> <p>"; echo $_SESSION['username']; echo ", the username or password is fincorrect! Please try again </p> </span><br>";
						$_SESSION['incorrect_credentials'] = false;
					}
					if($_SESSION['invalid_captcha'])
					{
						echo "
							<span class='error p-b-5'> <p>"; echo $_SESSION['username']; echo ", invalid captcha code! Please try again </p> </span> <br>";
						$_SESSION['invalid_captcha'] = false;
					}
					if($_SESSION['illegal_characters'])
					{
						echo "
							<span class='error p-b-5'> <p> "; echo $_SESSION['username']; echo " illegal characters were used in your username or password.</p>
								<p>The following characters are not allowed:</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp; &amp  &lt  &gt  &#40  &#41  &#123  &#125
								&#91 &#93  &#34  &#39  &#59  &#47  &#92 </p>
							</span>";
						$_SESSION['illegal_characters'] = false;
					}
					//? send user to the blocked page if the page is locked
					if($_SESSION['lockedTime'] > time())
					{
						header("Location: blocked.php");
					}
					?>
					<br>
					<div class="wrap-input100 validate-input m-b-23" data-validate = "Username is required">
						<span class="label-input100">Username</span>
						<input class="input100" type="text" name="username" placeholder="Type your username" autofocus>
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
								<br>
								<div class="wrap-input100 validate-input" data-validate="Captcha is required">
									<span class="label-input100">Captcha Code</span>
									<input class="input100" type="text" name="vercode" placeholder="Type the verification code" required="required">
									<span class="focus-input100" data-symbol="&#xf190;"></span>
								</div> <br>
								<div class="form-group small clearfix">
									<span class="label-input100">Captcha</span>
										<label class="checkbox-inline">Verification Code</label>
										&nbsp;&nbsp;<img src="captcha.php" >
									<span class="focus-input100" data-symbol="&#xf190;"></span>
        						</div>';
						}
					?>
					
					<div class="text-right p-t-8 p-b-31">

					</div>
					
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" type="submit" id="login_button">
								Login
							</button>
						</div>
					</div>

					<div class="flex-col-c p-t-30">
						<span class="txt1 p-b-2">
                            <a href="signup.html.php" class="txt2" id="signup_button">
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
</body>
</html>

