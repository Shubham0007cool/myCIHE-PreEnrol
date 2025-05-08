<?php
session_start();
require_once "db.php";
require_once "mail_functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    
    if (empty($email)) {
        $_SESSION["error"] = "Please enter your email address.";
        header("Location: ../index.php");
        exit;
    }
    
    $user_type = strpos($email, '@admin.com') !== false ? 'admin' : 'student';
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    
    // Set expiry time to 24 hours from now using UTC timezone
    date_default_timezone_set('UTC');
    $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // First, clear any existing reset tokens for this user
    $table = $user_type . 's';
    $clear_sql = "UPDATE $table SET reset_token = NULL, reset_expiry = NULL WHERE email = ?";
    if ($clear_stmt = mysqli_prepare($conn, $clear_sql)) {
        mysqli_stmt_bind_param($clear_stmt, "s", $email);
        mysqli_stmt_execute($clear_stmt);
        mysqli_stmt_close($clear_stmt);
    }
    
    // Store new token in database
    $sql = "UPDATE $table SET reset_token = ?, reset_expiry = ? WHERE email = ?";
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($conn) > 0) {
                if (sendResetEmail($email, $token)) {
                    $_SESSION["success"] = "Password reset instructions have been sent to your email address. Please check your inbox.";
                    header("Location: ../index.php");
                    exit;
                } else {
                    $_SESSION["error"] = "Failed to send email. Please try again later.";
                    header("Location: ../index.php");
                    exit;
                }
            } else {
                $_SESSION["error"] = "No account found with that email address.";
                header("Location: ../index.php");
                exit;
            }
        } else {
            $_SESSION["error"] = "Something went wrong. Please try again later.";
            header("Location: ../index.php");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?> 