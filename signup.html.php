    <?php
    session_start();
    if($_SESSION['lockedTime'] < time())
    {
        if(!isset($_SESSION['username_exists']))
        {
            $_SESSION['username_exists'] = false;
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
                    <form class="login100-form validate-form" action="signup.php" method="POST">
                        <span class="login100-form-title p-b-49">
                            Sign Up
                        </span>
                        <?php
                            if($_SESSION['username_exists'])
                            {
                                echo "
                                <span class='error p-b-49'>
                                    <span class='welcome p-b-5'> Username already is use </span>
                                </span>";
                                $_SESSION['username_exists'] = false;
                            }
                            if($_SESSION['passwords_not_matching'])
                            {
                                echo "
                                <span class='error p-b-49'>
                                    <span class='welcome p-b-5'> Passwords do not match! </span>
                                </span>";
                                $_SESSION['passwords_not_matching'] = false;
                            }
                            if($_SESSION['password_too_short'])
                            {
                                echo "
                                <span class='error p-b-49'>
                                    <span class='welcome p-b-5'> Password is too short!\nYour password should have at least 3 characters </span>
                                </span>";
                                $_SESSION['password_too_short'] = false;
                            }
                            if($_SESSION['password_needs_other_type_of_characters'])
                            {
                                echo "
                                <span class='error p-b-49'>
                                    <span class='welcome p-b-5'> The password should have: minimum: 8 characters, 1 number, 1 uppercase letter, 1 lowercase letter and 1 symbol </span>
                                </span>";
                                $_SESSION['password_needs_other_type_of_characters'] = false;
                            }
                            if($_SESSION['username_in_password'])
                            {
                                echo "
                                <span class='error p-b-49'>
                                    <span class='welcome p-b-5'> The password cannot contain the username in it\nPlease try again </span>
                                </span>";
                                $_SESSION['username_in_password'] = false;
                            }
                        ?>
                        <br>
                        <div class="wrap-input100 validate-input m-b-23" data-validate = "Username is required">
                            <span class="label-input100">Username</span>
                            <input class="input100" type="text" name="sign_up_username" placeholder="Type your username" required>
                            <span class="focus-input100" data-symbol="&#xf206;"></span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="Password is required">
                            <span class="label-input100">Password</span>
                            <input class="input100" type="password" name="pass1" placeholder="Type your password" required>
                            <span class="focus-input100" data-symbol="&#xf190;"></span>
                        </div>
                        
                        <div class="wrap-input100 validate-input" data-validate="Password is required">
                            <input class="input100" type="password" name="pass2" placeholder="Re-enter your password" required>
                            <span class="focus-input100" data-symbol="&#xf190;"></span>
                        </div>
                        
                        <div class="text-right p-t-8 p-b-31">
                            <a href="index.php">
                                Already have an account?
                            </a>
                        </div>
                        
                        <div class="container-login100-form-btn">
                            <div class="wrap-login100-form-btn">
                                <div class="login100-form-bgbtn"></div>
                                <button class="login100-form-btn" type="submit" >
                                    Sign Up
                                </button>
                            </div>
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
<?php
}
else
{
    header("Location: blocked.php.");
}
?>