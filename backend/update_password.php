<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    $_SESSION['error'] = "Please log in to change your password.";
    header('Location: ../index.php');
    exit;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Check for empty input
    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Validate passwords match
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Basic password length check
    if (strlen($new_password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Prepare and execute update
    $update_sql = "UPDATE students SET password = ? WHERE student_id = ?";
    $stmt = $conn->prepare($update_sql);

    if ($stmt) {
        $stmt->bind_param("ss", $hashed_password, $_SESSION["student_id"]);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Password updated successfully.";
        } else {
            $_SESSION['error'] = "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header('Location: ../index.php');
    exit;
}
?>
