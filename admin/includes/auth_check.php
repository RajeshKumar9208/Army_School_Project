<?php
function validateAdminSession() {
    global $con;
    
    if (empty($_SESSION['aid'])) {
        header('Location: index.php');
        exit();
    }

    require_once 'session_tracker.php';
    
    // Verify session in database
    $stmt = $con->prepare("SELECT current_session_id FROM tbladmin WHERE ID = ?");
    $stmt->bind_param("i", $_SESSION['aid']);
    $stmt->execute();
    $stmt->bind_result($dbSessionId);
    $stmt->fetch();
    $stmt->close();
    
    if ($dbSessionId !== session_id()) {
        session_unset();
        session_destroy();
        header('Location: index.php?error=session_mismatch');
        exit();
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
}
?>