<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($token) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION["error"] = "All fields are required";
        header("Location: ../reset_password.php?token=" . urlencode($token));
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION["error"] = "Passwords do not match";
        header("Location: ../reset_password.php?token=" . urlencode($token));
        exit;
    }

    // Simple password validation - just check length
    if (strlen($password) < 6) {
        $_SESSION["error"] = "Password must be at least 6 characters long";
        header("Location: ../reset_password.php?token=" . urlencode($token));
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password in students table
    $sql_students = "UPDATE students SET password = ?, reset_token = NULL, reset_expiry = NULL 
                    WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()";
    
    $updated = false;
    
    // Try updating students table
    if ($stmt = mysqli_prepare($conn, $sql_students)) {
        mysqli_stmt_bind_param($stmt, "sss", $hashed_password, $email, $token);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $updated = true;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Try updating admins table
    $sql_admins = "UPDATE admins SET password = ?, reset_token = NULL, reset_expiry = NULL 
                  WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()";
    
    if ($stmt = mysqli_prepare($conn, $sql_admins)) {
        mysqli_stmt_bind_param($stmt, "sss", $hashed_password, $email, $token);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $updated = true;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    if ($updated) {
        $_SESSION["success"] = "Password has been reset successfully. You can now login with your new password.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION["error"] = "Reset link has expired or is invalid";
        header("Location: ../reset_password.php?token=" . urlencode($token));
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?> 