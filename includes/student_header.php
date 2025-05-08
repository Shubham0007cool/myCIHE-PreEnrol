<?php


if (!isset($_SESSION["student_id"])) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - CIHE</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .session-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            animation: slideIn 0.5s ease-out, fadeOut 0.5s ease-out 4.5s forwards;
        }
        
        .session-message.success {
            background-color: #28a745;
        }
        
        .session-message.error {
            background-color: #dc3545;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    </style>
</head>
<body id="blurTarget">
    <?php if (isset($_SESSION['success'])): ?>
    <div class="session-message success">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="session-message error">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
    <?php endif; ?>

    <div class="log">
        <p>Logged in as Student (<?php echo $_SESSION["student_id"]; ?>)</p>
    </div>

    <div class="nav" style="z-index: 1000;">
        <div class="logo">
            <a href="home.php">
                <img src="image.png" alt="Logo">
            </a>
        </div>
        <button class="hamburger" aria-label="Toggle Navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="links">
            <ul>
                <li class="dropdown">
                    <a href="home.php">Dashboard</a>
                    <ul class="dropdown-menu">
                        <li><a class="orange" href="profile.php">Profile</a></li>
                    </ul>
                </li>
                <li><a href="subjectreg.php">Subject Registration</a></li>
                <li><a href="Support.php">Support</a></li>
                <li><a href="about.php">About</a></li>
                <li class="dropdown">
                    <a href="#">Setting</a>
                    <ul class="dropdown-menu">
                        <li><a href="#popForm">Change Password</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <header class="loggedIn">
        <h2>Welcome, Student</h2>
        <div class="buttons">
            <i class="fa-solid fa-bell"></i>
            <button id="destroy" onclick="return showModel()"><a href="logout.php"><i class="fa-solid fa-door-open"></i> Logout</a></button>
        </div>
    </header>

    <script>
        document.querySelector('.hamburger').addEventListener('click', () => {
            document.querySelector('.links').classList.toggle('active');
        });

        function showModel() {
            document.getElementById("logoutMode").style.display = "flex";
            document.body.classList.add("blurred");
            return false;
        }

        function hideModel() {
            document.getElementById("logoutMode").style.display = "none";
            document.body.classList.remove("blurred");
        }

        function confirmLogout() {
            window.location.href = "logout.php";
        }
    </script>

    <div class="confirm-btns" id="logoutMode" style="display: none; justify-content: center;">
        <div class="comfirm-box">
            <p class="sure">Are you sure to logout?</p>
            <div class="confirm-buttons">
                <button onclick="return confirmLogout()" class="yes-btn">Yes</button>
                <button onclick="return hideModel()" class="no-btn">No</button>
            </div>
        </div>
    </div>
