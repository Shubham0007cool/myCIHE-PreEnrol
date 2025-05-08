<?php
session_start();
require_once 'backend/db.php';

// Check if user is logged in as admin
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../index.php");
    exit;
}

// Get admin info
$admin_id = $_SESSION["admin_id"];
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CIHE</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .header {
            background: linear-gradient(to right, #c0c0c0, #ffffff);
            padding: 15px;
            display: flex;
            position: sticky;
            top: 0;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #000;
        }

        .logo {
            width: 50px;
            height: 50px;
        }

        .nav {
            display: flex;
            gap: 50px;
            font-weight: bold;
            margin: auto;
        }

        .nav a {
            text-decoration: none;
            color: black;
            margin: auto;
        }

        .nav a:hover {
            cursor: pointer;
            text-decoration: underline;
        }

        .nav-link.active {
            color: #4CAF50;
            text-decoration: underline;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background: #c0c0c0;
            min-width: 200px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            top: 100%;
            left: 0;
            cursor: pointer;
            z-index: 10;
        }

        .dropdown-content a {
            display: block;
            padding: 10px;
            color: black;
            text-decoration: none;
            font-weight: bold;
        }

        .dropdown-content a:hover {
            background: #a9a9a9;
            cursor: pointer;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .enrollment-time {
            font-size: 0.9em;
            color: #666;
        }

        .enrollment-faculty {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin-left: 8px;
        }

        .faculty-it {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .faculty-business {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .faculty-accounting {
            background-color: #f3e5f5;
            color: #6a1b9a;
        }

        .session-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
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

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close:hover {
            color: #000;
        }

        .password-form {
            margin-top: 20px;
        }

        .password-form .form-group {
            margin-bottom: 15px;
        }

        .password-form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .password-form input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .password-form button {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        .password-form button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="CIHE_logo.png" alt="Logo" class="logo">
        <div class="nav">
            <div class="dropdown">
                <a href="admin.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">Dashboard</a>
                <div class="dropdown-content">
                    <a href="search.php">Select Course and Units</a>
                    <a href="add_units.php">Add Units</a>
                    <a href="add_teacher.php">Add Teacher</a>
                    <a href="add_course.php">Add Course</a>
                </div>
            </div>
            <a href="studentpp.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'studentpp.php' ? 'active' : ''; ?>">Student Profile</a>
            <a href="logout.php" id="logout">Logout</a>
            <div class="dropdown">
                <a href="#">Settings</a>
                <div class="dropdown-content">
                    <a href="#popForm">Change Password</a>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Password Change Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Change Password</h2>
            <form id="passwordForm" class="password-form">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>

    <script>
        // Auto-hide session messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.session-message');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.opacity = '0';
                    message.style.transform = 'translateX(100%)';
                    setTimeout(() => message.remove(), 500);
                }, 5000);
            });
        });

        // Password change functionality
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('passwordModal');
            const closeBtn = document.querySelector('.close');
            const passwordForm = document.getElementById('passwordForm');
            const changePasswordLink = document.querySelector('a[href="#popForm"]');

            // Open modal
            changePasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                modal.style.display = 'block';
            });

            // Close modal
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Handle form submission
            passwordForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (newPassword !== confirmPassword) {
                    alert('New passwords do not match');
                    return;
                }

                const formData = new FormData(this);
                formData.append('action', 'change_password');

                try {
                    const response = await fetch('backend/admin_operations.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Password changed successfully');
                        modal.style.display = 'none';
                        passwordForm.reset();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while changing the password');
                }
            });
        });
    </script>

<?php
// Include live chat after all headers and session operations
include 'live_chat.php';
?> 