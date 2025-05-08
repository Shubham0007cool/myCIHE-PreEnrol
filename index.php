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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="script.js">
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="home.php">
                <img src="image.png" alt="Logo">
            </a>
        </div>
       <!--
       <style>
        @media screen and (max-width: 480px) {
            .navbar, .logo img{
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .navbar .link {
                
                text-align: center;
                margin-right: 70%;
               
                font-size: 10px;

            }
        
        }
        </style> -->
        <div class="link1">
            <p class="logged">Not logged in yet?</p>
            <ul>
                <li><a href="#popForm"><i class="fas fa-key"></i>Forgot Password?</a></li>
            </ul>
        </div>
    </div>
    <div id="popForm" class="popUp">
        <a href="#" class="close-btn">✖</a>
        <form class="popMe" method="POST" action="backend/password_reset.php">
            <h3><i class="fas fa-key"></i> Reset Password</h3>

            <label for="email">Email Address <span class="astrick">*</span></label>
            <input class="user" type="email" id="email" name="email" required>
            <small>Enter the email address you used to register</small>
            <button class="change" type="submit">Send Reset Link</button>
        </form>
    </div>
    <style>
        .popUp {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            width: 90%;
            max-width: 400px;
        }
        .popUp h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .popUp .close-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            text-decoration: none;
            color: #666;
            font-size: 20px;
        }
        .popUp .close-btn:hover {
            color: #333;
        }
        .popUp label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .popUp input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .popUp small {
            display: block;
            color: #666;
            margin-bottom: 15px;
            font-size: 0.85em;
        }
        .popUp button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .popUp button:hover {
            background-color: #45a049;
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
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ef9a9a;
            text-align: center;
        }
    </style>
    <div class="welcome">
        <i class="fas fa-desktop"></i><span>Welcome to CIHE Pre-enrollment System</span>
    </div>

    <hr>
<script>
function getForm(event) {
            event.preventDefault(); // Prevent form submission
            
            var email = document.forms["loginform"]["email"].value;
            var password = document.forms["loginform"]["password"].value;

            if (!email || !password) {
                alert("Please, fill both email and password.");
                return false;
            }

            // if (email.includes('@admin.com')) {
            //     // Admin login - show admin dashboard
            //     alert("Logged in as admin!");
            //     window.location.href = "admin.php";
            //     return true; // Prevent form submission
            // } else {
            //     // Regular user login - redirect to home.php
            //     alert("Successfully logged in as student!");
            //     window.location.href = "home.php"; // Redirect to home page
            //     return true;
            // }
        }

</script>  

<div class="container">

    <div class="login-box">

    <div>
<?php
            if (isset($_SESSION["success"])) {
                echo '<div class="success-message">' . htmlspecialchars($_SESSION["success"]) . '</div>';
                unset($_SESSION["success"]);
            }
            if (isset($_SESSION["error"])) {
                echo '<div class="error-message">' . htmlspecialchars($_SESSION["error"]) . '</div>';
                unset($_SESSION["error"]);
            }
            ?>
</div>

        <h2 class="CIHE">CIHE Login</h2>
        <?php
        if (isset($_SESSION["error"])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION["error"]) . '</div>';
            unset($_SESSION["error"]);
        }
        ?>
        <form method="POST" action="backend/login.php">
            <label class="align" for="email">Email</label>
            <input class="getit" placeholder="Enter @admin.com if admin/@gmail.com if student" type="email" id="email" name="email" required>
            <label class="align" for="password">Password</label>
            <input class="getit" placeholder="Enter password" type="password" id="password" name="password" required>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <p class="sign">Don't have an account? 
            <a href="student_signup.php">Register</a>
        </p>
    </div>
            
    <div class="info-box">
        <img class="image" src="image.png" alt="CIHE Logo" class="logo">
        <p>Welcome to CIHE pre-enrolment website. Register yourself for an easy enrolment process with your time.</p>
    </div>
</div>

    </div>
    
    <footer>
        <div>
            &copy; Copyright 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved. Privacy Policy | Terms of Use
        </div>
</footer>

<script>
document.getElementById('loginform').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('backend/login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = data.redirect;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>

</body>
</html>
