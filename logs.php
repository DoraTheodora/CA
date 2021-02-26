<?php
    session_start();
    header("Content-Security-Policy: frame-ancestors 'none'", false);
	header('X-Frame-Options: SAMEORIGIN');
    if(isset($_SESSION["id_s"]) && session_id() == $_SESSION["id_s"])
	{
        echo'
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
                                //TODO: make the menu damn fine!
                            }
                            echo '
                            <span class="login100-form-title p-b-20">
                                    <span class="txt1 p-b-2">
                                        <a href="profile.php" class="txt2">
                                            Profile
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
    echo "<script>
            alert('You are not logged in. Access denied'); 
            window.location.href='index.php';
        </script>";
}
?>

<?php
function get_log()
{
    include 'conf.php';
    $sql = "SELECT * FROM Logs";
    if($query = $conn->query($sql))
    {
        echo '
        <span class="login100-form-title p-b-20">
                Activity logs <br><br>
            </span>';
        echo "<table class='styled-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ACTION PERFORMED</th>
                        <th>IP</th>
                        <th>DATE TIME</th>
                        <th>OUTCOME</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class='active-row'>
                    ";        
        while($row = $query->fetch_assoc())
        {
            echo "<tr>";
            echo "<td>".$row["id"]."</td>";
            echo "<td>".$row["action_performed"]."</td>";
            echo "<td>".$row["ip"]."</td>";
            echo "<td>".$row["date_time"]."</td>";
            echo "<td>". $row["outcome"]."</td>";
            echo "</tr>";
        }
        echo '
            </tr>
            </table>';
    }
    $query->free();
}
?>