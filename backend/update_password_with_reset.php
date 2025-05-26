<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: ../reset_password.php?token=' . urlencode($token));
        exit;
    }
    
    // Simple password validation - just check length
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
        header('Location: ../reset_password.php?token=' . urlencode($token));
        exit;
    }
    
    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Update password in database for students
    $update_student_sql = "UPDATE students SET password = ?, reset_token = NULL, reset_expiry = NULL 
                          WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()";
    
    $stmt = $conn->prepare($update_student_sql);
    $stmt->bind_param("sss", $hashed_password, $email, $token);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Password has been reset successfully. Please login with your new password.";
            header('Location: ../index.php');
            exit;
        }
    }
    $stmt->close();
    
    // If student update didn't work, try updating teacher
    $update_teacher_sql = "UPDATE teachers SET password = ?, reset_token = NULL, reset_expiry = NULL 
                          WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()";
    
    $stmt = $conn->prepare($update_teacher_sql);
    $stmt->bind_param("sss", $hashed_password, $email, $token);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Password has been reset successfully. Please login with your new password.";
            header('Location: ../index.php');
            exit;
        }
    }
    $stmt->close();
    
    // If neither update worked
    $_SESSION['error'] = "Invalid or expired reset token. Please request a new password reset.";
    header('Location: ../reset_password.php?token=' . urlencode($token));
    exit;
} else {
    header('Location: ../index.php');
    exit;
}
?> 
