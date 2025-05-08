<?php
require_once 'includes/student_header.php';
require_once 'includes/live_chat.php';
require_once 'includes/password_form.php';
require_once 'backend/course_operations.php';

// Get all courses
$courses = getCourses();
?>

<style>
    /* Additional styles for the unit details modal */
    .unit-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        z-index: 1000;
        overflow-y: auto;
    }
    
    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 20px;
        width: 70%;
        max-width: 800px;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    
    .close-modal {
        float: right;
        font-size: 24px;
        cursor: pointer;
    }
    
    .unit-details {
        margin-top: 20px;
    }
    
    .unit-details h3 {
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .unit-details p {
        margin: 10px 0;
    }
    
    .view-details-btn {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }
    
    .view-details-btn:hover {
        background-color: #2980b9;
    }

    .course-card {
        text-align: center;
        margin: 20px;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-5px);
    }

    .course-card img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
    }

    .course-card p {
        margin: 10px 0;
        color: #333;
        font-weight: 500;
    }
</style>

<div class="head">
    <h1 style="justify-content: center; text-align: center;">Search Course</h1>
</div>

<div class="locations">
    <select id="locationFilter">
        <option value="">All Locations</option>
        <option value="Gungahlin">Gungahlin</option>
        <option value="Belconnen">Belconnen</option>
        <option value="City">City</option>
    </select>
</div>

<hr>

<div class="search">
    <i class="fa-solid fa-search"></i>
    <input type="search" id="courseSearch" placeholder="Search units...">
</div>

<h1 class="available" style="text-align: center; color: gray;">
    Please select your courses to register.
</h1>

<div class="images" id="courseContainer">
    <?php foreach ($courses as $course): ?>
    <div class="course-card">
        <a href="registration.php?course=<?php echo urlencode($course['code']); ?>">
            <img class="subs" src="<?php echo htmlspecialchars($course['image_path']); ?>" 
                 alt="<?php echo htmlspecialchars($course['name']); ?>">
        </a>
        <hr>
        <p><?php echo htmlspecialchars($course['name']); ?></p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Unit Details Modal -->
<div id="unitModal" class="unit-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="unitDetails" class="unit-details">
            <!-- Unit details will be inserted here -->
        </div>
    </div>
</div>

<script>
    // DOM elements
    const searchInput = document.getElementById('courseSearch');
    const locationFilter = document.getElementById('locationFilter');
    const courseContainer = document.getElementById('courseContainer');
    const unitModal = document.getElementById('unitModal');
    const unitDetails = document.getElementById('unitDetails');
    const closeModal = document.querySelector('.close-modal');

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Setup search functionality
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const cards = document.querySelectorAll('.course-card');
            
            cards.forEach(card => {
                const courseName = card.querySelector('p').textContent.toLowerCase();
                if (courseName.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // Close modal when clicking X
        closeModal.addEventListener('click', function() {
            unitModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === unitModal) {
                unitModal.style.display = 'none';
            }
        });
    });
</script>

<footer>
    <div>
        &copy; Copyright 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved. Privacy Policy | Terms of Use
    </div>
</footer>
</body>
</html>
