<?php
    session_start();
    if($_SESSION['lockedTime'] > time())
    {
        //echo $_SESSION['lockedTime'];
        echo '
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
                                     We are sorry, this device is blocked 
                                 </span>
                                 <span class="login100-form-title p-b-49">
                                     Try again in about 3 minutes
                                 </span>
                                 <div class="flex-col-c p-t-30">
                                 <a href="index.php" class="txt2 p-t-100">
                                    Home Page
                                </a>
                                </div>
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
         </html>';
    }
    else
    {
        $_SESSION['login_attempts'] = 0;
        header("Location: index.php");
    }
?>

