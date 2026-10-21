<?php
// SECURE SESSION CONFIGURATION
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// MUST INCLUDE THESE FIRST
require_once 'includes/config.php';
require_once 'includes/session_tracker.php';

// START SESSION
session_start();

// DEBUG LOGGING
error_log("Logout initiated for session: ".session_id());

// SESSION TERMINATION
if (isset($_SESSION['aid'])) {
    // 1. Clear from database FIRST
    clearActiveSession($_SESSION['aid']);
    
    // 2. Destroy PHP session
    session_unset();
    
    // 3. Remove session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), 
            '', 
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // 4. Destroy session file
    session_destroy();
    
    // 5. Additional cleanup
    $sessionFile = session_save_path().'/sess_'.session_id();
    if (file_exists($sessionFile)) {
        unlink($sessionFile);
    }
}

// PREVENT CACHING
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// REDIRECT
header("Location: index.php?logout=1");
exit();
?>