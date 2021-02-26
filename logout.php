<?php
    header("Content-Security-Policy: frame-ancestors 'none'", false);
    header('X-Frame-Options: SAMEORIGIN');
    session_start();
    echo $_SESSION["name"];
    session_unset();
    session_destroy();
    //echo session_id();
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0 "); // Proxies.
    header('Location: login.html.php');
?>