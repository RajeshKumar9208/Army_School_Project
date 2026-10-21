<?php
function preventConcurrentLogins($adminId) {
    global $con;

    $currentSessionId = session_id();
    $now = date('Y-m-d H:i:s');

    // Get existing session data from DB
    $stmt = $con->prepare("SELECT current_session_id, last_activity FROM tbladmin WHERE ID = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $stmt->bind_result($activeSessionId, $lastActivity);
    $stmt->fetch();
    $stmt->close();

    // Check if there's another active session
    if (!empty($activeSessionId) && $activeSessionId !== $currentSessionId) {
        $lastActiveTime = strtotime($lastActivity);
        $sessionTimeout = 1800; // 30 minutes

        if ((time() - $lastActiveTime) < $sessionTimeout) {
            // Session is still active, deny login
            session_unset();
            session_destroy();
            header('Location: index.php?error=concurrent');
            exit();
        } else {
            // Stale session, clear it
            $update = $con->prepare("UPDATE tbladmin SET current_session_id = NULL, last_activity = NULL WHERE ID = ?");
            $update->bind_param("i", $adminId);
            $update->execute();
            $update->close();
        }
    }

    // Set current session as active
    $update = $con->prepare("UPDATE tbladmin SET current_session_id = ?, last_activity = ? WHERE ID = ?");
    $update->bind_param("ssi", $currentSessionId, $now, $adminId);
    $update->execute();
    $update->close();
}

function clearActiveSession($adminId) {
    global $con;

    $update = $con->prepare("UPDATE tbladmin SET current_session_id = NULL, last_activity = NULL WHERE ID = ?");
    $update->bind_param("i", $adminId);
    $update->execute();
    $update->close();
}
?>
