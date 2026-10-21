<?php
// ========================
// ERROR REPORTING (FOR DEBUGGING)
// ========================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ========================
// SECURITY CONFIGURATION
// ========================
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable if using HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800); // 30-minute session timeout

// Set SameSite attribute for session cookie
session_set_cookie_params([
    'lifetime' => 0,  // Session duration (0 = until browser closes)
    'path' => '/',    // Cookie accessible across entire domain
    'domain' => $_SERVER['HTTP_HOST'], // Current domain
    'secure' => true, // Only send over HTTPS
    'httponly' => true, // Not accessible via JavaScript
    'samesite' => 'Lax' // Recommended SameSite setting
]);

// Start or regenerate session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    session_regenerate_id(true);
}

// ========================
// FILE INCLUDES
// ========================
require_once 'includes/config.php';

// ========================
// SECURITY HEADERS
// ========================
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'");
header("Referrer-Policy: strict-origin-when-cross-origin");

// ========================
// SESSION VALIDATION
// ========================
if (!isset($_SESSION['aid']) || strlen($_SESSION['aid']) == 0) {
    header('Location: index.php');
    exit();
}

// ========================
// CSRF PROTECTION
// ========================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Initialize error/success messages
$error = '';
$success = '';

// ========================
// PASSWORD CHANGE HANDLER
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change'])) {
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("Security token invalid. Please refresh the page and try again.");
        }

        // Get input values with null coalescing
        $admid = $_SESSION['aid'];
        $currentPassword = $_POST['currentpassword'] ?? '';
        $newPassword = $_POST['newpassword'] ?? '';
        $confirmPassword = $_POST['confirmpassword'] ?? '';

        // Basic validation
       
        if (empty($newPassword)) {
            throw new Exception("New password is required");
        }
        if (empty($confirmPassword)) {
            throw new Exception("Password confirmation is required");
        }

        // Verify current password
        $stmt = $con->prepare("SELECT Password FROM tbladmin WHERE ID = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $con->error);
        }
        
        $stmt->bind_param("i", $admid);
        if (!$stmt->execute()) {
            throw new Exception("Failed to verify current password");
        }
        
        $stmt->bind_result($dbPassword);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($currentPassword, $dbPassword)) {
            throw new Exception("Current password is incorrect");
        }

        // Check if new password matches current
        if ($currentPassword === $newPassword) {
            throw new Exception("New password cannot be the same as current password");
        }

        // Password complexity requirements
        if (strlen($newPassword) < 8) {
            throw new Exception("Password must be at least 8 characters");
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            throw new Exception("Password must contain at least one uppercase letter");
        }
        if (!preg_match('/[a-z]/', $newPassword)) {
            throw new Exception("Password must contain at least one lowercase letter");
        }
        if (!preg_match('/[0-9]/', $newPassword)) {
            throw new Exception("Password must contain at least one number");
        }
        if (!preg_match('/[\W]/', $newPassword)) {
            throw new Exception("Password must contain at least one special character");
        }

        // Confirm password match
        if ($newPassword !== $confirmPassword) {
            throw new Exception("New password and confirmation do not match");
        }

        // Hash new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password in database
        $update = $con->prepare("UPDATE tbladmin SET Password = ? WHERE ID = ?");
        if (!$update) {
            throw new Exception("Database error: " . $con->error);
        }
        
        $update->bind_param("si", $newPasswordHash, $admid);
        if (!$update->execute()) {
            throw new Exception("Failed to update password");
        }
        $update->close();

        // Regenerate session for security
        session_unset();
session_destroy();

// Start fresh session just to carry the success message temporarily
session_start();
session_regenerate_id(true);

// Store success message in the new session
$_SESSION['password_change_success'] = "Password changed successfully. Please login again.";

// Force reauthentication
header('Location: index.php?reason=password_changed');
exit();
        
        // Replace the current success handling with:
$success = "Password changed successfully. You will be logged out for security reasons.";
echo "<script>setTimeout(function(){ window.location.href = 'logout.php'; }, 3000);</script>";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password | Balvatika APS Basoli</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  
  <style>
    body {
        background: linear-gradient(to right, #005aa7, #fffde4);
        font-family: 'Source Sans Pro', sans-serif;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    
    .content-wrapper {
        background: transparent;
    }
    
    .card {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border: none;
    }
    
    .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    
    .card-title {
        color: #005aa7;
        font-weight: 700;
    }
    
    .form-control {
        height: 45px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
    
    .btn-primary {
        background-color: #005aa7;
        border-color: #005aa7;
        font-weight: 600;
        height: 45px;
        border-radius: 8px;
        transition: background 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #004080;
        border-color: #004080;
    }
    
    .password-meter {
        height: 5px;
        background: #e0e0e0;
        margin-top: 5px;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .password-meter-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s ease;
    }
    
    .password-requirements {
        margin-top: 10px;
        font-size: 13px;
    }
    
    .password-requirements li {
        list-style-type: none;
        position: relative;
        padding-left: 20px;
        margin-bottom: 5px;
    }
    
    .password-requirements li:before {
        content: "✗";
        color: #dc3545;
        position: absolute;
        left: 0;
    }
    
    .password-requirements li.valid:before {
        content: "✓";
        color: #28a745;
    }
    
    /* Prevent auto-fill styling */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px white inset !important;
        -webkit-text-fill-color: #333 !important;
        transition: background-color 5000s ease-in-out 0s !important;
    }
    
    /* Error message styling */
    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include_once("includes/navbar.php"); ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include_once("includes/sidebar.php"); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Change Password</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
              <li class="breadcrumb-item active">Change Password</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Change your Password</h3>
              </div>
              
              <?php if (!empty($error)): ?>
              <div class="alert alert-danger mx-3 mt-3">
                <?php echo htmlspecialchars($error); ?>
              </div>
              <?php elseif (!empty($success)): ?>
              <div class="alert alert-success mx-3 mt-3">
                <?php echo htmlspecialchars($success); ?>
              </div>
              <?php endif; ?>
              
              <form method="post" name="changepassword" autocomplete="off">
                <div class="card-body">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                  
                  <!-- Current Password -->
                  <div class="form-group">
                    <label for="currentpassword">Current Password</label>
                    <input type="password" class="form-control" id="currentpassword" name="currentpassword" 
                           required oncopy="return false" onpaste="return false" oncut="return false"
                           autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
                  </div>
                  
                  <!-- New Password -->
                  <div class="form-group">
                    <label for="newpassword">New Password</label>
                    <input type="password" class="form-control" id="newpassword" name="newpassword" 
                           required oncopy="return false" onpaste="return false" oncut="return false"
                           autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
                    <div class="alert alert-warning" id="password-match-error" style="display: none;">
                      New password cannot be the same as current password
                    </div>
                    <div class="password-meter">
                      <div class="password-meter-bar" id="password-strength"></div>
                    </div>
                    <ul class="password-requirements" id="password-requirements">
                      <li id="length">At least 8 characters</li>
                      <li id="uppercase">At least one uppercase letter</li>
                      <li id="lowercase">At least one lowercase letter</li>
                      <li id="number">At least one number</li>
                      <li id="special">At least one special character</li>
                    </ul>
                  </div>
                  
                  <!-- Confirm Password -->
                  <div class="form-group">
                    <label for="confirmpassword">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" 
                           required oncopy="return false" onpaste="return false" oncut="return false"
                           autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
                    <div class="invalid-feedback" id="password-match-feedback">
                      Passwords do not match
                    </div>
                  </div>
                </div>
                
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" name="change" id="change">Change Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  
  <?php include_once('includes/footer.php'); ?>
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<script>
// Combined password validation
document.getElementById('newpassword').addEventListener('input', function() {
    const currentPassword = document.getElementById('currentpassword').value;
    const newPassword = this.value;
    const confirmPassword = document.getElementById('confirmpassword').value;
    const strengthBar = document.getElementById('password-strength');
    const changeButton = document.getElementById('change');
    
    // Check if new password matches current
    const isSamePassword = currentPassword && newPassword === currentPassword;
    document.getElementById('password-match-error').style.display = isSamePassword ? 'block' : 'none';
    
    // Password strength requirements
    const requirements = {
        length: newPassword.length >= 8,
        uppercase: /[A-Z]/.test(newPassword),
        lowercase: /[a-z]/.test(newPassword),
        number: /[0-9]/.test(newPassword),
        special: /[\W_]/.test(newPassword)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(key => {
        const element = document.getElementById(key);
        element.classList.toggle('valid', requirements[key]);
    });
    
    // Calculate strength score (0-100)
    const score = Object.values(requirements).filter(Boolean).length * 20;
    strengthBar.style.width = score + '%';
    strengthBar.style.backgroundColor = score < 40 ? '#dc3545' : 
                                     score < 70 ? '#ffc107' : '#28a745';
    
    // Check password match
    const passwordsMatch = newPassword === confirmPassword;
    document.getElementById('confirmpassword').classList.toggle('is-invalid', confirmPassword && !passwordsMatch);
    
    // Enable/disable submit button
    const allRequirementsMet = Object.values(requirements).every(v => v);
    changeButton.disabled = isSamePassword || !allRequirementsMet || !passwordsMatch;
});

// Confirm password validation
document.getElementById('confirmpassword').addEventListener('input', function() {
    const newPassword = document.getElementById('newpassword').value;
    const confirmPassword = this.value;
    const passwordsMatch = newPassword === confirmPassword;
    
    this.classList.toggle('is-invalid', confirmPassword && !passwordsMatch);
    document.getElementById('change').disabled = confirmPassword && !passwordsMatch;
});

// Clear clipboard and prevent paste for password fields
document.addEventListener('copy', function(e) {
    if (document.activeElement.type === 'password') {
        e.preventDefault();
    }
});

document.addEventListener('paste', function(e) {
    if (document.activeElement.type === 'password') {
        e.preventDefault();
    }
});

// Clear fields on page load
window.addEventListener('load', function() {
    document.querySelectorAll('input[type="password"]').forEach(input => {
        input.value = '';
    });
});
</script>
</body>
</html>