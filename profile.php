<?php
require_once 'includes/student_header.php';
require_once 'includes/live_chat.php';
require_once 'includes/password_form.php';
require_once 'backend/profile_operations.php';

// Get user data from database
$student_id = $_SESSION["student_id"];
$user_data = getStudentProfile($student_id);
$error = null;

if (!$user_data) {
    $error = "User profile not found";
}

// Get student enrollments
$enrollments = getStudentEnrollments($student_id);

// Get student notifications
$notifications = getStudentNotifications($student_id);
?>

<div class="tab-navigation">
    <button class="tab-btn active" onclick="openTab('profile-tab')">Profile</button>
    <button class="tab-btn" onclick="openTab('edit-profile-tab')">Edit Profile</button>
</div>

<?php if ($error): ?>
    <div class="error-message"><?php echo $error; ?></div>
<?php endif; ?>

<div id="profile-tab" class="tab-content active">
    <div class="subnav">
        <div class="sub-contents">
            <div class="initials">
                <h1 class="initial1"><?php echo substr($user_data['first_name'] ?? '', 0, 1) . substr($user_data['last_name'] ?? '', 0, 1); ?></h1>
            </div>
            <div class="initialname">
                <h1 class="initial2"><?php echo htmlspecialchars($user_data['first_name'] ?? '') . ' ' . htmlspecialchars($user_data['last_name'] ?? ''); ?></h1>
            </div>
        </div>
        <div class="details-container">
            <div class="details1">
                <h2 style="font-size: 20px;">User details</h2>
                <h3 style="font-size: 10px;"><a href="#" id="getEdit" onclick="openTab('edit-profile-tab'); return false;">Edit profile</a></h3>
                <h2 style="font-size: 20px;">Email Address</h2>
                <p style="color: blue;"><?php echo htmlspecialchars($user_data['email'] ?? 'Not Set'); ?></p>
                <h2 style="font-size: 20px;">Phone</h2>
                <p style="color: blue;"><?php echo htmlspecialchars($user_data['contact_number'] ?? 'Not Set'); ?></p>
                <h2 style="font-size: 20px;">Program</h2>
                <p style="color: blue;"><?php echo htmlspecialchars($user_data['program_name'] ?? 'Not Set'); ?> (<?php echo htmlspecialchars($user_data['program_code'] ?? ''); ?>)</p>
            </div>

            <div class="details2">
                <h2 style="font-size: 20px;">Emergency Contact</h2>
                <div class="emergency-info">
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($user_data['emergency_contact'] ?? 'Not Set'); ?></p>
                </div>
            </div>

            <div class="details2">
                <h2 style="font-size: 20px;">Enrolled Units</h2>
                <div class="enrollments-info">
                    <?php if (!empty($enrollments)): ?>
                        <ul>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <li>
                                    <?php echo htmlspecialchars($enrollment['unit_code'] . ' - ' . $enrollment['unit_name']); ?>
                                    <br>
                                    <small>Status: <?php echo htmlspecialchars($enrollment['status']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No enrollments found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="edit-profile-tab" class="tab-content">
    <div class="heading">
        <h1 class="Education"><i class="fa-solid fa-user"></i> Edit Your Profile</h1>
    </div>       
    
    <form action="backend/profile_operations.php" method="POST" id="profileMe" class="getForm">
        <input type="hidden" name="action" value="update_profile">
        <div class="container5">
            <h1 class="perform"><i class="fa-solid fa-circle-user"></i> Personal Information</h1>       
            <label class="full" for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" placeholder="Enter first name" 
                   value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" required>
            
            <label class="full" for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" placeholder="Enter last name" 
                   value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" required>
            
            <label class="full" for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email"
                   value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
            
            <label class="full" for="contact_number">Phone</label>
            <input type="text" name="contact_number" id="contact_number" placeholder="Enter your number"
                   value="<?php echo htmlspecialchars($user_data['contact_number'] ?? ''); ?>" required>
            
            <label class="full" for="emergency_contact">Emergency Contact</label>
            <input type="text" name="emergency_contact" id="emergency_contact" placeholder="Enter emergency contact"
                   value="<?php echo htmlspecialchars($user_data['emergency_contact'] ?? ''); ?>" required>
        </div>
        
        <div class="btn-container">
            <button type="submit" class="button">Update Profile</button>
        </div>
    </form>
</div>

<script>
    // Tab Navigation
    function openTab(tabId) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });
        
        // Show the selected tab content
        document.getElementById(tabId).classList.add('active');
        
        // Find and activate the corresponding tab button
        const tabBtn = document.querySelector(`.tab-btn[onclick="openTab('${tabId}')"]`);
        if (tabBtn) {
            tabBtn.classList.add('active');
        }
    }

    // Form submission handling
    document.getElementById('profileMe').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('backend/profile_operations.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Failed to update profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the profile');
        });
    });
</script>

<style>
.error-message {
    background-color: #dc3545;
    color: white;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
}

.emergency-info, .enrollments-info {
    margin-top: 10px;
}

.emergency-info p, .enrollments-info p {
    margin: 5px 0;
    color: blue;
}

.emergency-info strong {
    color: #333;
}

.enrollments-info ul {
    list-style: none;
    padding: 0;
}

.enrollments-info li {
    margin: 10px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.enrollments-info small {
    color: #666;
}
</style>

<footer>
    <div>
        &copy; Copyright 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved. Privacy Policy | Terms of Use
    </div>
</footer>
</body>
</html>
