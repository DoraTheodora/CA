    <?php
    require 'security_methods.php';
    header("Content-Security-Policy: frame-ancestors 'none'", false);
	header('X-Frame-Options: SAMEORIGIN');
	header('X-XSS-Protection: 1; mode=block');
	header('X-Frame-Options: DENY');
	header('X-Content-Type-Options: nosniff');
	session_cache_limiter('nocache');
    
    session_start();
    is_user_locked();
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
            <div class="container-login100">
                <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
                    <form class="login100-form validate-form" action="signup.php" method="POST">
                        <div class="info">
                            To create a new account you should have:
                            <ul>
                                <li> &nbsp;&nbsp;&nbsp;&nbsp; A unique username </li>
                                <li> &nbsp;&nbsp;&nbsp;&nbsp; A password that: </li>
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
                            Sign Up
                        </span>
                        <?php
                            if($_SESSION['username_exists'])
                            {
                                echo "
                                    <span class='error p-b-5'> <p> Username already is use </p></span>";
                                $_SESSION['username_exists'] = false;
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
                            <a href="login.html.php">
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
    </body>
    </html>
<?php
}
else
{
    header("Location: blocked.php.");
}
?>