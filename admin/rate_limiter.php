<?php
function enforceRateLimit($maxAttempts = 5, $lockoutMinutes = 15) {
    global $con;
    
    // Exclude these paths from rate limiting
    $excludedPaths = ['logout.php', 'healthcheck.php', 'dashboard.php'];
    foreach ($excludedPaths as $path) {
        if (strpos($_SERVER['REQUEST_URI'], $path) !== false) {
            return;
        }
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $now = date('Y-m-d H:i:s');

    try {
        // Only increment attempts for login pages (index.php)
        if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) {
            $query = "INSERT INTO rate_limits (ip, attempts, last_attempt) 
                     VALUES (?, 1, ?)
                     ON DUPLICATE KEY UPDATE 
                        attempts = IF(
                            last_attempt < DATE_SUB(NOW(), INTERVAL 1 HOUR) OR
                            (blocked_until IS NOT NULL AND blocked_until < NOW()),
                            1,
                            attempts + 1
                        ),
                        last_attempt = VALUES(last_attempt),
                        blocked_until = IF(
                            attempts + 1 >= ?,
                            DATE_ADD(NOW(), INTERVAL ? MINUTE),
                            blocked_until
                        )";
            
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssii", $ip, $now, $maxAttempts, $lockoutMinutes);
            $stmt->execute();
        }

        // Check current status
        $status = $con->prepare("SELECT attempts, blocked_until FROM rate_limits WHERE ip = ?");
        $status->bind_param("s", $ip);
        $status->execute();
        $result = $status->get_result();
        $data = $result->fetch_assoc();

        if ($data && $data['attempts'] >= $maxAttempts && 
            strtotime($data['blocked_until']) > time()) {
            http_response_code(429);
            header('Retry-After: '.($lockoutMinutes * 60));
            die("Too many requests. Please try again later.");
        }
    } catch (Exception $e) {
        error_log("Rate limiter error: " . $e->getMessage());
        // Fail open in production, but you might want to fail closed in some cases
    }
}
?>