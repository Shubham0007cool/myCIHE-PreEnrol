<?php
require_once 'includes/admin_header.php';
require_once 'backend/student_operations.php';
?>

<div class="student-profile-container">
    <div class="search-container">
        <h3>Search Student Profiles</h3>
        <input type="text" id="studentSearch" placeholder="Search by Name or ID..." onkeyup="searchStudents()">
        <div class="student-results">
            <h4>Search Results</h4>
            <ul id="resultsList">
                <!-- Results will be populated dynamically -->
            </ul>
        </div>
    </div>

    <div id="profileDetails" class="profile-card" style="display:none;">
        <div class="profile-header">
            <img id="profilePic" src="default-profile.jpg" alt="Profile Picture" class="profile-pic">
            <h2 id="profileName"></h2>
            <p id="profileId"></p>
            <p id="profileProgram" class="program-badge"></p>
        </div>
        <div class="profile-details">
            <h3>Contact Details</h3>
            <ul>
                <li><strong>Email:</strong> <span id="profileEmail"></span></li>
                <li><strong>Phone:</strong> <span id="profilePhone"></span></li>
                <li><strong>Emergency Contact:</strong> <span id="profileEmergency"></span></li>
            </ul>
            <h3>Course Enrollments</h3>
            <ul id="courseList">
                <!-- Course list will be populated dynamically -->
            </ul>
        </div>
    </div>
</div>

<div class="footer">
    Copyright &copy; 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved.<br>
    Privacy Policy | Terms of Use
</div>

<script>
    // Search students function
    async function searchStudents() {
        const searchTerm = document.getElementById('studentSearch').value;
        const resultsList = document.getElementById('resultsList');
        
        try {
            const response = await fetch('backend/student_operations.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    search: searchTerm
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Clear previous results
                resultsList.innerHTML = '';
                
                if (data.students.length === 0) {
                    resultsList.innerHTML = '<li>No students found matching your search.</li>';
                    return;
                }
                
                // Display filtered results
                data.students.forEach(student => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${student.name} - ${student.student_id}`;
                    listItem.onclick = () => showStudentProfile(student);
                    resultsList.appendChild(listItem);
                });
            } else {
                console.error('Search failed:', data.message);
                resultsList.innerHTML = '<li>Error searching students. Please try again.</li>';
            }
        } catch (error) {
            console.error('Error:', error);
            resultsList.innerHTML = '<li>Error searching students. Please try again.</li>';
        }
    }

    // Show student profile function
    function showStudentProfile(student) {
        const profileDetails = document.getElementById('profileDetails');
        
        // Update profile information
        document.getElementById('profileName').textContent = student.name;
        document.getElementById('profileId').textContent = `Student ID: ${student.student_id}`;
        document.getElementById('profileEmail').textContent = student.email;
        document.getElementById('profilePhone').textContent = student.phone || 'Not provided';
        document.getElementById('profileEmergency').textContent = student.emergency_contact || 'Not provided';
        document.getElementById('profileProgram').textContent = student.program || 'Program not assigned';
        
        // Update course list
        const courseList = document.getElementById('courseList');
        courseList.innerHTML = '';
        
        if (student.courses && student.courses.length > 0) {
            student.courses.forEach(course => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<strong>${course}</strong>`;
                courseList.appendChild(listItem);
            });
        } else {
            courseList.innerHTML = '<li>No courses enrolled</li>';
        }
        
        // Show the profile section
        profileDetails.style.display = 'block';
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        searchStudents();
    });
</script>

<style>
    .student-profile-container {
        display: flex;
        gap: 20px;
        padding: 20px;
    }
    
    .search-container {
        flex: 1;
        max-width: 400px;
    }
    
    #studentSearch {
        width: 90%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .student-results {
        background: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .student-results h4 {
        margin-top: 0;
        color: #2c3e50;
    }
    
    #resultsList {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    #resultsList li {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    
    #resultsList li:hover {
        background-color: #f5f5f5;
    }
    
    .profile-card {
        flex: 2;
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .profile-header {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #3498db;
    }
    
    .program-badge {
        display: inline-block;
        padding: 5px 10px;
        background-color: #3498db;
        color: white;
        border-radius: 15px;
        font-size: 14px;
        margin-top: 5px;
    }
    
    .profile-details h3 {
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }
    
    .profile-details ul {
        list-style: none;
        padding: 0;
    }
    
    .profile-details li {
        margin-bottom: 8px;
    }
</style>
</body>
</html>
