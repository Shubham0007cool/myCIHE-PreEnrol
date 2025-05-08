<?php
session_start();

// if user is logged in, redirect to home.php
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="script.js">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ef9a9a;
            text-align: center;
        }
        @media (max-width: 480px){
            .container1{
                padding: 20px;
                width: 85%;    
            }
            h1 {
                font-size: 1.5rem;
            }
            input {
                font-size: 0.9rem;
                padding: 8px;
            }
            .btn-2 button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container1">
        <div class="img">
            <img src="image.png" alt="Logo">
        </div>
        <h1>Sign up</h1>
        <?php
        if (isset($_SESSION["error"])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION["error"]) . '</div>';
            unset($_SESSION["error"]);
        }
        ?>
        <div class="formIt">
            <form action="backend/register.php" method="POST">
                <label for="first_name">First Name</label>
                <input class="full" type="text" name="first_name" id="first_name" placeholder="Enter first name" value="<?php echo isset($_SESSION['form_data']['first_name']) ? htmlspecialchars($_SESSION['form_data']['first_name']) : ''; ?>" required>

                <label for="last_name">Last Name</label>
                <input class="full" type="text" name="last_name" id="last_name" placeholder="Enter last name" value="<?php echo isset($_SESSION['form_data']['last_name']) ? htmlspecialchars($_SESSION['form_data']['last_name']) : ''; ?>" required>

                <label for="email">Email</label>
                <input class="full" type="email" name="email" id="email" placeholder="Enter CIHE student email (e.g., cihe12020@student.cihe.edu.au)" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
                <small>Must be a valid CIHE student email address</small>

                <label for="student_id">Student ID</label>
                <input class="full" type="text" name="student_id" id="student_id" placeholder="Enter your student ID" value="<?php echo isset($_SESSION['form_data']['student_id']) ? htmlspecialchars($_SESSION['form_data']['student_id']) : ''; ?>" required>

                <label for="password">Password</label>
                <input class="full" type="password" name="password" id="password" placeholder="Enter password" required>
                <small>Password must be at least 6 characters long</small>

                <label for="confirm_password">Confirm Password</label>
                <input class="full" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>

                <label for="contact_number">Contact Number</label>
                <input class="full" type="tel" name="contact_number" id="contact_number" placeholder="Enter your contact number" value="<?php echo isset($_SESSION['form_data']['contact_number']) ? htmlspecialchars($_SESSION['form_data']['contact_number']) : ''; ?>" required>

                <label for="emergency_contact">Emergency Contact</label>
                <input class="full" type="tel" name="emergency_contact" id="emergency_contact" placeholder="Enter emergency contact number" value="<?php echo isset($_SESSION['form_data']['emergency_contact']) ? htmlspecialchars($_SESSION['form_data']['emergency_contact']) : ''; ?>" required>

                <label for="program_id">Program</label>
                <select class="unitss" name="program_id" id="program_id" required>
                    <option value="">Select your program</option>
                    <option value="1" <?php echo (isset($_SESSION['form_data']['program_id']) && $_SESSION['form_data']['program_id'] == '1') ? 'selected' : ''; ?>>Masters in IT</option>
                    <option value="2" <?php echo (isset($_SESSION['form_data']['program_id']) && $_SESSION['form_data']['program_id'] == '2') ? 'selected' : ''; ?>>Bachelor of IT</option>
                    <option value="3" <?php echo (isset($_SESSION['form_data']['program_id']) && $_SESSION['form_data']['program_id'] == '3') ? 'selected' : ''; ?>>Bachelor of Early Childhood</option>
                    <option value="4" <?php echo (isset($_SESSION['form_data']['program_id']) && $_SESSION['form_data']['program_id'] == '4') ? 'selected' : ''; ?>>Bachelor of Accounting</option>
                </select>

                <div class="btn-2">
                    <button type="submit" id="register">Register</button>
                </div>
            </form>
        </div>
        <div class="login">
            <span>Already have an account? <a href="index.php">Login</a></span>
        </div>
    </div>

    <footer>
        <div class="logoo">
            <img src="CIHE.png" alt="CIHE Logo">
        </div>
        <div>
            &copy; Copyright 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved. Privacy Policy | Terms of Use
        </div>
    </footer>
</body>
</html>
