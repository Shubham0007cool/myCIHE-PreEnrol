<?php
session_start();
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: ../index.php");
    exit;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION["student_id"];
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validate passwords
    if ($new_password !== $confirm_password) {
        $_SESSION["error"] = "New passwords do not match!";
        header("Location: ../home.php");
        exit;
    }
    
    // Validate password format
    if (!preg_match("/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$?])[A-Za-z\d@#$?]{6,30}$/", $new_password)) {
        $_SESSION["error"] = "Password must be 6-10 characters long and contain letters, numbers, and special characters (@#$?)";
        header("Location: ../home.php");
        exit;
    }
    
    try {
        // Get current password from database
        $stmt = $conn->prepare("SELECT password FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("User not found");
        }
        
        $user = $result->fetch_assoc();
        
        // Verify old password
        if (!password_verify($old_password, $user["password"])) {
            $_SESSION["error"] = "Current password is incorrect!";
            header("Location: ../home.php");
            exit;
        }
        
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in database
        $stmt = $conn->prepare("UPDATE students SET password = ? WHERE student_id = ?");
        $stmt->bind_param("ss", $hashed_password, $student_id);
        
        if ($stmt->execute()) {
            $_SESSION["success"] = "Password updated successfully!";
        } else {
            throw new Exception("Failed to update password");
        }
        
    } catch (Exception $e) {
        $_SESSION["error"] = "An error occurred: " . $e->getMessage();
    }
    
    header("Location: ../home.php");
    exit;
} else {
    // If not POST request, redirect to home
    header("Location: ../home.php");
    exit;
}
?> 