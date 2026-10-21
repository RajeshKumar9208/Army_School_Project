<?php
session_start();

// Verify session fingerprint
if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || 
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_tampered");
    exit();
}

// Check session expiration (30 mins)
if (time() - $_SESSION['created'] > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_expired");
    exit();
}
?>