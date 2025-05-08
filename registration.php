<?php
require_once 'backend/course_operations.php';
require_once 'backend/registration_operations.php';

// Now include the header and other files that might output content
require_once 'includes/student_header.php';

// Check if user is logged in
if (!isset($_SESSION["student_id"])) {
    header('Location: login.php');
    exit;
}

// Get course code from URL
$course_code = isset($_GET['course']) ? $_GET['course'] : '';

// Get units for the selected course
$units = [];
if ($course_code) {
    $units = getUnitsByCourseCode($course_code);
}

// Get course name from first unit (if any)
$course_name = !empty($units) ? $units[0]['course_name'] : '';

// If no course selected or no units found, redirect back
if (empty($units)) {
    header('Location: subjectreg.php');
    exit;
}

require_once 'includes/password_form.php';
require_once 'includes/live_chat.php';
?>

   
    <div class="location1">
        <select id="locationFilter">
            <option value="">Select locations</option>
            <option value="Gungahlin">Gungahlin</option>
            <option value="Belconnen">Belconnen</option>
            <option value="City">City</option>
        </select>
    </div>

    <p class="Reso" style="padding: 20px;">Please select the subject and desired class session(s) and click Register button.</p>
    <hr>
   
<form id="registrationForm">
<div class="subjects">
     <h2 style="text-align: center; font-size: 40px; margin-top: -10px;"><?php echo htmlspecialchars($course_name); ?></h2>
    <?php foreach ($units as $unit): ?>
    <div class="subject1">
        <h3 class="elective" style="text-align: center;">
            <?php echo htmlspecialchars($unit['code']); ?>: <?php echo htmlspecialchars($unit['name']); ?> 
            (Core-CP: <?php echo htmlspecialchars($unit['credits']); ?>.00)
        </h3>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Teacher</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="checkbox" name="units[]" value="<?php echo htmlspecialchars($unit['id']); ?>" 
                               class="unit-checkbox" data-unit="<?php echo htmlspecialchars($unit['code']); ?>">
                        <?php echo htmlspecialchars($unit['code']); ?>
                    </td>
                    <td>
                        <select name="teacher_<?php echo htmlspecialchars($unit['id']); ?>" 
                                class="teacher-select">
                            <option value="">Select teacher...</option>
                            <?php if (isset($unit['teacher_id']) && $unit['teacher_id']): ?>
                            <option value="<?php echo htmlspecialchars($unit['teacher_id']); ?>">
                                <?php echo htmlspecialchars($unit['teacher_name'] ?? 'Not Assigned'); ?>
                            </option>
                            <?php else: ?>
                            <option value="0">Not Assigned</option>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td>
                        <select name="day_<?php echo htmlspecialchars($unit['id']); ?>" 
                                class="day-select">
                            <option value="">Select day...</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </td>
                    <td>
                        <select name="time_<?php echo htmlspecialchars($unit['id']); ?>" 
                                class="time-select">
                            <option value="">Select time...</option>
                            <option value="8:30am-11:30am">8:30am-11:30am</option>
                            <option value="6:00pm-9:00pm">6:00pm-9:00pm</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
</div>
<div class="btn-container">
    <button type="submit" id="register" class="button">Register</button>
</div>
</form>
<div class="back" style="text-align: center; margin-top: 40px;">
    <span><a  style="color: black; cursor: pointer;" href="subjectreg.php"><i style="margin-right: 10px; cursor: pointer; color: black;" class="fa fa-arrow-left"></i>Back to Course Selection</a></span>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const checkboxes = document.querySelectorAll('.unit-checkbox');
    
    // Enable/disable selects based on checkbox state
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const unitId = this.value;
            const relatedSelects = document.querySelectorAll(`select[name^="teacher_${unitId}"], 
                                                           select[name^="day_${unitId}"], 
                                                           select[name^="time_${unitId}"]`);
            
            relatedSelects.forEach(select => {
                select.disabled = !this.checked;
                if (!this.checked) {
                    select.value = '';
                }
            });
        });
    });
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const checkedUnits = document.querySelectorAll('.unit-checkbox:checked');
        if (checkedUnits.length === 0) {
            alert('Please select at least one unit to register');
            return;
        }
        
        // Validate all required fields for checked units
        let isValid = true;
        checkedUnits.forEach(checkbox => {
            const unitId = checkbox.value;
            const teacher = document.querySelector(`select[name="teacher_${unitId}"]`).value;
            const day = document.querySelector(`select[name="day_${unitId}"]`).value;
            const time = document.querySelector(`select[name="time_${unitId}"]`).value;
            
            if (!teacher || !day || !time) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            alert('Please fill in all required fields for selected units');
            return;
        }
        
        // Prepare data for submission
        const unitsData = Array.from(checkedUnits).map(checkbox => ({
            unit_id: checkbox.value,
            teacher_id: document.querySelector(`select[name="teacher_${checkbox.value}"]`).value,
            day: document.querySelector(`select[name="day_${checkbox.value}"]`).value,
            time: document.querySelector(`select[name="time_${checkbox.value}"]`).value
        }));
        
        try {
            const response = await fetch('backend/registration_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(unitsData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.href = 'profile.php';
            } else {
                alert(result.message || 'Registration failed. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    });
});
</script>

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
