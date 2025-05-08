<?php
session_start();
require_once "db.php";
require_once "mail_functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $student_id = trim($_POST["student_id"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $program_id = trim($_POST["program_id"]);
    $contact_number = trim($_POST["contact_number"]);
    $emergency_contact = trim($_POST["emergency_contact"]);
    
    // Store form data in session for persistence
    $_SESSION['form_data'] = [
        'student_id' => $student_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'program_id' => $program_id,
        'contact_number' => $contact_number,
        'emergency_contact' => $emergency_contact
    ];
    
    // Validate input
    $errors = [];
    
    if (empty($student_id)) {
        $errors[] = "Student ID is required";
    }
    
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@student\.cihe\.edu\.au$/', $email)) {
        $errors[] = "Email must be a valid CIHE student email (e.g., cihe12020@student.cihe.edu.au)";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must have at least 6 characters";
    }
    
    if ($password != $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($program_id)) {
        $errors[] = "Program selection is required";
    }
    
    if (empty($contact_number)) {
        $errors[] = "Contact number is required";
    }
    
    if (empty($emergency_contact)) {
        $errors[] = "Emergency contact is required";
    }
    
    // Check if email already exists
    $sql = "SELECT id FROM students WHERE email = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "This email is already registered";
        }
        mysqli_stmt_close($stmt);
    }
    
    // Check if student ID already exists
    $sql = "SELECT id FROM students WHERE student_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "This student ID is already registered";
        }
        mysqli_stmt_close($stmt);
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $sql = "INSERT INTO students (student_id, first_name, last_name, email, password, program_id, contact_number, emergency_contact) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ssssssss", 
                $student_id, 
                $first_name, 
                $last_name, 
                $email, 
                $hashed_password,
                $program_id,
                $contact_number,
                $emergency_contact
            );
            
            if (mysqli_stmt_execute($stmt)) {
                // Send welcome email using the new function
                $mail_sent = sendWelcomeEmail($email, $first_name, $student_id);
                
                // Clear form data from session
                unset($_SESSION['form_data']);
                
                // Set success message and redirect
                $_SESSION["success"] = "Registration successful!" . ($mail_sent ? " Welcome email has been sent." : " However, there was an issue sending the welcome email.");
                header("Location: ../index.php");
                exit;
            } else {
                $_SESSION["error"] = "Something went wrong. Please try again later.";
                header("Location: ../student_signup.php");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $_SESSION["error"] = implode(", ", $errors);
        header("Location: ../student_signup.php");
        exit;
    }
}

mysqli_close($conn);
?> 