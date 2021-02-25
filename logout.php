<?php
    session_start();
    echo $_SESSION["name"];
    session_unset();
    session_destroy();
    //echo session_id();
    header('Location: index.php');
?>