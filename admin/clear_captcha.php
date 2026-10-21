<?php
// File: clear_captcha.php
session_start();
unset($_SESSION['captcha_question']);
unset($_SESSION['captcha_answer']);
exit();
?>