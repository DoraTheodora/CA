<?php
	header("Content-Security-Policy: frame-ancestors 'none'", false);
	header('X-Frame-Options: SAMEORIGIN');
	session_start();
	if(isset($_SESSION["id_s"]) && session_id() == $_SESSION["id_s"])
	{
		//echo $_SESSION["id_s"];
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
					<form action>
						<div class="limiter">
							<span class="author p-b-49" > Theodora Tataru, C00231174 <br> 2021 </span> 
							<div class="container-login100">
								<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
									<form class="login100-form validate-form">
										<span class="login100-form-title p-b-49">
											Welcome
											<?php
												echo $_SESSION['name'];
											?>! 
											<br>
											<span class="welcome p-b-5">This is your personal page</span>
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
													<a href="turtle.php" class="txt2">
														Pretty Turtle
													</a>
												</span>
												<span class="txt1 p-b-2">
													<a href="change_password.html.php" class="txt2">
														Change Password
													</a>
												</span>
												<span class="txt1 p-b-2">
													<a href="php_info.php" class="txt2">
														PHP Info
													</a>
												</span>
												<span class="txt1 p-b-2">
													<a href="logout.php" class="txt2">
														Log out
													</a>
												</span>
											</div>
										</span>
									</form>
								</div>
							</div>
						</div>
					</form>

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
		}
	}
	else
	{
		header("Location: login.html.php");
	}
?>
</html>