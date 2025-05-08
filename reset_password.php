<?php
session_start();
require_once "backend/db.php";

$token = $_GET['token'] ?? '';
$valid_token = false;
$email = '';
$error = '';

if (empty($token)) {
    header("Location: index.php");
    exit;
}

// Check if token exists and is not expired
$sql = "SELECT email, reset_expiry FROM students WHERE reset_token = ? AND reset_expiry > NOW() UNION 
        SELECT email, reset_expiry FROM admins WHERE reset_token = ? AND reset_expiry > NOW()";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $token, $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $valid_token = true;
        $email = $row['email'];
        $expiry = strtotime($row['reset_expiry']);
        $now = time();
        
        if ($now > $expiry) {
            $error = "Reset link has expired. Please request a new password reset.";
            $valid_token = false;
        }
    } else {
        // Check if token exists but is expired
        $sql = "SELECT email FROM students WHERE reset_token = ? UNION 
                SELECT email FROM admins WHERE reset_token = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $token, $token);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $error = "Reset link has expired. Please request a new password reset.";
            } else {
                $error = "Invalid reset link. Please request a new password reset.";
            }
        }
    }
    mysqli_stmt_close($stmt);
} else {
    $error = "Database error. Please try again later.";
}

// Debug information (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] === 'true') {
    echo "Token: " . htmlspecialchars($token) . "<br>";
    echo "Valid Token: " . ($valid_token ? 'Yes' : 'No') . "<br>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Error: " . htmlspecialchars($error) . "<br>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CIHE</title>
    <link rel="stylesheet" href="index.css">
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
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #a5d6a7;
            text-align: center;
        }
        .reset-form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .reset-form h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .reset-form label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .reset-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .reset-form small {
            display: block;
            color: #666;
            margin-bottom: 15px;
            font-size: 0.85em;
        }
        .reset-form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .reset-form button:hover {
            background-color: #45a049;
        }
        .reset-form .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
        }
        .reset-form .back-link:hover {
            color: #333;
        }
        .password-requirements {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 0.85em;
            color: #666;
        }
        .password-requirements ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        .password-requirements li {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="reset-form">
        <h2><i class="fas fa-key"></i> Reset Password</h2>
        <?php
        if (isset($_SESSION["error"])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION["error"]) . '</div>';
            unset($_SESSION["error"]);
        }
        if (isset($_SESSION["success"])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION["success"]) . '</div>';
            unset($_SESSION["success"]);
        }
        if ($error) {
            echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
        }
        ?>
        
        <?php if ($valid_token): ?>
            <form method="POST" action="backend/update_password.php" onsubmit="return validateForm()">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required 
                       
                       minlength="6">
                
                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <ul>
                        <li>Must be at least 6 characters long</li>
                    </ul>
                </div>
                
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                
                <button type="submit">Reset Password</button>
                <a href="index.php" class="back-link">Back to Login</a>
            </form>
        <?php else: ?>
            <p><a href="index.php" class="back-link">Return to Login</a></p>
        <?php endif; ?>
    </div>

    <script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return false;
        }
        
        // const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$?])[A-Za-z\d@#$?]{6,10}$/;
        // if (!passwordPattern.test(password)) {
        //     alert('Password does not meet the requirements!');
        //     return false;
        // }
        
        return true;
    }
    </script>
</body>
</html> 