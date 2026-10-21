<?php
function enforceSingleSession($adminId, $username) {
    global $con;
    
    $stmt = $con->prepare("SELECT active_session_id FROM tbladmin WHERE ID = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $stmt->bind_result($activeSessionId);
    $stmt->fetch();
    $stmt->close();
    
    if (!empty($activeSessionId)) {
        // Only block if the existing session is still active
        if (session_status($activeSessionId) === PHP_SESSION_ACTIVE && 
            $activeSessionId !== session_id()) {
            $_SESSION['login_error'] = 'This account is already logged in elsewhere.';
            header('Location: index.php');
            exit();
        }
    }
    
    // Update with current session ID
    $update = $con->prepare("UPDATE tbladmin SET active_session_id = ? WHERE ID = ?");
    $update->bind_param("si", session_id(), $adminId);
    $update->execute();
    $update->close();
}

function clearActiveSession($adminId) {
    global $con;
    $update = $con->prepare("UPDATE tbladmin SET active_session_id = NULL WHERE ID = ?");
    $update->bind_param("i", $adminId);
    if (!$update->execute()) {
        error_log("Failed to clear active session for admin ID: $adminId");
    }
    $update->close();
}
?>