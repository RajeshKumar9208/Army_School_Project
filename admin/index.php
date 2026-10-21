<?php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
// Set security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Expect-CT: max-age=86400, enforce, report-uri='https://balvatikaapsbasoli.org/report-ct'");

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-Permitted-Cross-Domain-Policies: none");
header("Expect-CT: max-age=86400, enforce");
header("Permissions-Policy: geolocation=(), microphone=()");
// ========================
// ERROR REPORTING
// ========================

require_once 'includes/config.php';
// ========================
// SECURITY CONFIGURATION
// ========================
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Only enable if using HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800);

session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);



session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
// ========================
// FILE INCLUDES
// ========================
require_once 'includes/config.php';
require_once 'rate_limiter.php';
require_once 'includes/session_tracker.php';
require_once 'MathCaptcha.php';

enforceRateLimit(); // Rate limit login attempts
if (empty($_SESSION['captcha_question'])) {
    $captcha = new MathCaptcha();
    $_SESSION['captcha_question'] = $captcha->getQuestion();
    $_SESSION['captcha_answer'] = $captcha->getAnswer();
}
// ========================
// SECURITY HEADERS
// ========================
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'");
header("Referrer-Policy: strict-origin-when-cross-origin");

// ========================
// HTTPS REDIRECT
// ========================
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect, true, 301);
    exit();
}

// ========================
// SESSION SECURITY
// ========================
if (empty($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
} elseif ($_SESSION['fingerprint'] !== hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'])) {
    session_unset();
    session_destroy();
    die("Session fingerprint mismatch. Please log in again.");
}

// ========================
// CSRF TOKEN INIT
// ========================
if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// ========================
// INPUT SANITIZER
// ========================
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// ========================
// LOGIN LOGGING
// ========================
function logFailedLogin($username) {
    global $con;
    
    // File logging (works even if DB fails)
    $ip = $_SERVER['REMOTE_ADDR'];
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $log = sprintf("[%s] Failed login: %s from %s | UA: %s\n", date("Y-m-d H:i:s"), $username, $ip, $ua);
    file_put_contents('secure_logs/failed_logins.log', $log, FILE_APPEND);

    // Database logging (with error handling)
    if ($con) {
        try {
            $stmt = $con->prepare("INSERT INTO login_attempts (username, ip, user_agent) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $username, $ip, $ua);
                $stmt->execute();
                $stmt->close();
            } else {
                error_log("Failed to prepare statement for login attempt logging");
            }
        } catch (Exception $e) {
            error_log("Database error in logFailedLogin: " . $e->getMessage());
        }
    } else {
        error_log("No database connection in logFailedLogin");
    }
}

// ========================
// LOGIN HANDLER
// ========================
// ========================
// LOGIN HANDLER
// ========================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Verify CSRF token first
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }
    if (!isset($_POST['captcha_answer']) || !filter_var($_POST['captcha_answer'], FILTER_VALIDATE_INT) || 
        (int)$_POST['captcha_answer'] !== $_SESSION['captcha_answer']) {
        
        // Regenerate CAPTCHA for next attempt
        unset($_SESSION['captcha_question']);
        unset($_SESSION['captcha_answer']);
        
        $_SESSION['login_error'] = 'Incorrect CAPTCHA answer. Please try again.';
        header('Location: index.php');
        exit();
    }
    // Decrypt AES-encrypted fields from client (must match JS key/IV)
function decryptData($data) {
    $key = "pL@9xZ#7vTb!dQ3R"; // same as used in CryptoJS
    $iv = "kM^2sG$8bWu@eL1N";  // same as used in CryptoJS

    return openssl_decrypt(base64_decode($data), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}


$decryptedUsername = decryptData($_POST['username']);
$decryptedPassword = decryptData($_POST['inputpwd']);
$username = sanitizeInput($decryptedUsername);
$password = $decryptedPassword;


    // Debug: Log the received credentials (remove in production)
    error_log("Login attempt for: " . $username);

    $stmt = $con->prepare("SELECT ID, AdminuserName, Password, UserType, login_attempts, last_failed_attempt FROM tbladmin WHERE AdminuserName = ? LIMIT 1");
    if (!$stmt) {
        die("Database error: " . $con->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $dbUsername, $dbPassword, $userType, $attempts, $lastAttempt);
        $stmt->fetch();

        // Debug: Log the database values
        error_log("DB Password Hash: " . $dbPassword);
        error_log("User Type: " . $userType);

        // Lockout check
        if ($attempts >= 5 && strtotime($lastAttempt) > time() - 1800) {
            $wait = 1800 - (time() - strtotime($lastAttempt));
            die("Account locked. Try again in " . gmdate("i:s", $wait) . " minutes.");
        }

        // Password verification
        if (password_verify($password, $dbPassword)) {
            // Debug
            error_log("Password verification successful");

            // Regenerate session ID
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['aid'] = $id;
            $_SESSION['uname'] = $dbUsername;
            $_SESSION['utype'] = $userType;
            $_SESSION['last_activity'] = time();
            $_SESSION['fingerprint'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

            // Reset failed attempts
             $reset = $con->prepare("UPDATE rate_limits SET attempts = 0 WHERE ip = ?");
                 $reset->bind_param("s", $_SERVER['REMOTE_ADDR']);
             $reset->execute();
             $reset->close();

            // Regenerate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();

            // Debug session data
            error_log("Session data after login: " . print_r($_SESSION, true));

            // Clear output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Redirect to dashboard
            header("Location: dashboard.php");
            session_regenerate_id(true);
            exit();
        } else {
            // Debug
            error_log("Password verification failed");

            // Wrong password
            $update = $con->prepare("UPDATE tbladmin SET login_attempts = login_attempts + 1, last_failed_attempt = NOW() WHERE AdminuserName = ?");
            $update->bind_param("s", $username);
            $update->execute();
            $update->close();

            logFailedLogin($username);
            $_SESSION['login_error'] = 'Invalid username or password';
            header('Location: index.php');
            exit();
        }
    } else {
        // Debug
        error_log("User not found: " . $username);

        // No user found
        logFailedLogin($username);
        $_SESSION['login_error'] = 'Invalid username or password';
        header('Location: index.php');
        exit();
    }

    $stmt->close();
}

if (isset($con) && $con) {
    $con->close();
}
ob_end_flush();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | Balvatika APS Basoli</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link 
  rel="stylesheet" 
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
  integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
  crossorigin="anonymous" 
  referrerpolicy="no-referrer"
>
 <link 
  rel="stylesheet" 
  href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;400i;700&display=swap" 
  crossorigin="anonymous"
>

<style>
  body.login-page {
    background: linear-gradient(to right, #005aa7, #fffde4);
    font-family: 'Source Sans Pro', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
  }
/* Prevent text selection */
body {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Allow selection in input fields */
input, textarea {
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    user-select: text !important;
}
  .login-box {
    width: 100%;
    max-width: 420px;
  }

  .card {
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border: none;
    background: #ffffff;
  }

  .card-header {
    background: transparent;
    border-bottom: none;
    padding-top: 30px;
    text-align: center;
  }

  .card-header .h1 {
    font-size: 24px;
    color: #005aa7;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
  }

  .card-body {
    padding: 2rem;
  }

  .login-box-msg {
    font-size: 16px;
    font-weight: 500;
    color: #444;
    margin-bottom: 20px;
    text-align: center;
  }

  .form-control {
    height: 45px;
    font-size: 15px;
    border-radius: 8px 0 0 8px;
    border: 1px solid #ccc;
  }

  .input-group-text {
    border-radius: 0 8px 8px 0;
    background-color: #005aa7;
    color: white;
    border: 1px solid #005aa7;
  }

  .btn-primary {
    background-color: #005aa7;
    border-color: #005aa7;
    font-weight: 600;
    height: 45px;
    width: 100%;
    border-radius: 8px;
    transition: background 0.3s ease;
  }

 input[type="text"]{
     width:100%;
 }
  input[type="password"]{
     width:100%;
 }
  .btn-primary:hover {
    background-color: #004080;
    border-color: #004080;
  }

  .alert {
    background-color: #f8d7da;
    color: #842029;
    font-size: 14px;
    padding: 12px;
    border-radius: 6px;
    margin-top: 15px;
    text-align: center;
  }

  /* For smaller devices */
  @media (max-width: 480px) {
    .login-box {
      margin: 1rem;
    }
  }
  /* Disable auto-fill highlighting */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 1000px white inset !important;
    -webkit-text-fill-color: #333 !important;
    transition: background-color 5000s ease-in-out 0s !important;
}

/* Prevent browser from remembering fields */
input[type="text"], 
input[type="password"] {
    background-clip: text !important;
}
/* CAPTCHA specific styles */
.captcha-note {
    font-size: 12px;
    color: #666;
    text-align: center;
    margin-top: -10px;
    margin-bottom: 15px;
    font-style: italic;
}

.input-group-text .fa-calculator {
    color: white;
}
</style>


</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>Admin</b> | Balvatika APS Basoli</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>
<?php
// Display login error if exists
if (isset($_SESSION['login_error'])) {
    echo '<div class="alert alert-danger text-center">'.$_SESSION['login_error'].'</div>';
    unset($_SESSION['login_error']); // Clear the error after displaying
}
?>
      <form autocomplete="off" method="post" action="" onsubmit="return encryptLoginForm();">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" id="username" placeholder="Username" required oncopy="return false" onpaste="return false" oncut="return false"
       autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="inputpwd" id="password" placeholder="Password" required oncopy="return false" onpaste="return false" oncut="return false"
       autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
<!-- CAPTCHA Field -->
<div class="input-group mb-3">
    <input type="text" 
           class="form-control" 
           name="captcha_answer" 
           placeholder="<?php echo htmlspecialchars($_SESSION['captcha_question']); ?>" 
           required
           oncopy="return false" 
           onpaste="return false" 
           oncut="return false"
           autocomplete="off"
           pattern="-?\d*"
           inputmode="numeric"
           readonly 
           onfocus="this.removeAttribute('readonly')">
    <div class="input-group-append">
        <div class="input-group-text">
            <span class="fas fa-calculator"></span>
        </div>
    </div>
</div><br><br>
<p class="captcha-note">Please solve the math problem to prove you're human</p>

        <div class="row">
          <div class="col-4 offset-8">
            <button type="submit" class="btn btn-primary btn-block" name="login">Sign In</button>
          </div>
        </div>
      </form>

     
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script src="../js/encrypt.js"></script>
<script>
// Numeric validation for CAPTCHA field
document.querySelector('input[name="captcha_answer"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9-]/g, ''); // Allow numbers and minus sign
});

// Clear CAPTCHA on failed submission
if (document.querySelector('.alert.alert-danger')) {
    fetch('clear_captcha.php')
        .catch(err => console.error('CAPTCHA cleanup failed:', err));
}
// Clear fields on page load
window.addEventListener('load', function() {
    document.querySelector('input[name="username"]').value = '';
    document.querySelector('input[name="inputpwd"]').value = '';
});

// Prevent auto-fill after user interaction

// Disable right-click menu
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    alert('Right-click is disabled for security reasons');
});

// Disable text selection
document.addEventListener('selectstart', function(e) {
    e.preventDefault();
});

// Disable copy/paste/cut
document.addEventListener('cut', function(e) {
    e.preventDefault();
    alert('Cut operation is disabled for security reasons');
});

document.addEventListener('copy', function(e) {
    e.preventDefault();
    alert('Copy operation is disabled for security reasons');
});

document.addEventListener('paste', function(e) {
    e.preventDefault();
    alert('Paste operation is disabled for security reasons');
});

// Extra protection for password field
document.querySelector('input[name="inputpwd"]').addEventListener('paste', function(e) {
    e.preventDefault();
    alert('Pasting in password field is disabled for security reasons');
});

</script>



</body>
</html>
