<?php
session_start();
require_once "db.php";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    // Validate credentials
    if (empty($email) || empty($password)) {
        $_SESSION["error"] = "Please enter both email and password.";
        header("Location: ../index.php");
        exit;
    }

    // Check if user is admin
    if (strpos($email, '@admin.com') !== false || strpos($email, '@cihe.edu.au') !== false) {
        $sql = "SELECT id, email, password FROM admins WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_start();
                        
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["admin_id"] = $id;
                        $_SESSION["email"] = $email;
                        
                        header("Location: ../admin.php");
                        exit;
                    } else {
                        $_SESSION["error"] = "Invalid password.";
                        header("Location: ../index.php");
                        exit;
                    }
                }
            } else {
                $_SESSION["error"] = "No admin account found with that email.";
                header("Location: ../index.php");
                exit;
            }
        }
    } else {
        // Student login
        $sql = "SELECT id, email, password, student_id FROM students WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password, $student_id);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_start();
                        
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;
                        $_SESSION["student_id"] = $student_id;
                        
                        header("Location: ../home.php");
                        exit;
                    } else {
                        $_SESSION["error"] = "Invalid password.";
                        header("Location: ../index.php");
                        exit;
                    }
                }
            } else {
                $_SESSION["error"] = "No student account found with that email.";
                header("Location: ../index.php");
                exit;
            }
        }
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?> 