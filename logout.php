<?php
    header("Content-Security-Policy: frame-ancestors 'none'", false);
	header('X-Frame-Options: SAMEORIGIN');
	header('X-XSS-Protection: 1; mode=block');
	header('X-Frame-Options: DENY');
	header('X-Content-Type-Options: nosniff');
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0 "); // Proxies.
    require 'security_methods.php';
    
    
	session_cache_limiter('nocache');
    session_start();
    delete_session_at_log_out();
    session_unset();
    session_destroy();
    
    header('Location: login.html.php');
?>