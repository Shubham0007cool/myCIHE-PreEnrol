<?php
require_once 'includes/admin_header.php';
require_once 'backend/admin_operations.php';

?>

<style>
        .course-result {
            border: 1px solid #ddd;
            padding: 15px;
            width: 50%;
            justify-content: center;
            margin: auto;
            margin-bottom: 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .course-result:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-10px);
            cursor: pointer;
        }
        .course-result h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .view-stats-btn {
            padding: 12px 25px;
        border-radius: 30px;
        font-weight: 600;
        border: 1px solid gray;
        text-decoration: none;
        transition: all 0.3s ease;
        color: black;
        display: inline-block;
        }
        .view-stats-btn:hover {
        transform: translateY(-3px);
        text-decoration: none;
        color: white;
        box-shadow: 0 6px 20px rgba(238, 26, 26, 0.799);
        background-color: #e05d00;
        }
        
        .search-filters {
            display: flex;
            gap: 15px;
            margin: auto;
            justify-content: center;
            text-align: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-box{
            margin: auto;
            justify-content: center;
        }
        .filter-group {
            min-width: 200px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>

    <div class="search-container">
        <h1>Course Search</h1>
        
        <div class="search-filters">
            <div class="filter-group">
                <label for="faculty">Faculty:</label>
                <select id="faculty">
                    <option value="all">All Faculties</option>
                    <option value="it">Information Technology</option>
                    <option value="business">Business</option>
                    <option value="accounting">Accounting</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="program">Program:</label>
                <select id="program">
                    <option value="all">All Programs</option>
                    <option value="bachelor-it">Bachelor of IT</option>
                    <option value="master-it">Master of IT</option>
                    <option value="bachelor-accounting">Bachelor of Accounting</option>
                </select>
            </div>
        </div>   
        <div class="search-box">
            <input type="text" placeholder="Search courses..." id="courseSearch">
            <button onclick="searchCourses()">Search</button>
        </div>     
        <div class="search-results" id="searchResults">
            <div class="course-result">
                <h3>ICT305 - Topics in IT</h3>
                <p>Semester 2, 2024 | Teachers: Omar Shindi, Fareed Ud Din</p>
                <p>Bachelor of Information Technology</p>
                <a href="enrollmentstats.php?course=ICT305&name=Topics in IT&faculty=it&program=bachelor-it" class="view-stats-btn">View Enrollment Stats</a>
            </div>
            
            <div class="course-result">
                <h3>ICT701 - Advanced Cybersecurity</h3>
                <p>Semester 1, 2024 | Teachers: Fareed Ud Din</p>
                <p>Master of Information Technology</p>
                <a href="enrollmentstats.php?course=ICT701&name=Advanced Cybersecurity&faculty=it&program=master-it" class="view-stats-btn">View Enrollment Stats</a>
            </div>
            
            <div class="course-result">
                <h3>ACC201 - Financial Accounting</h3>
                <p>Semester 1, 2024 | Teachers: John Smith</p>
                <p>Bachelor of Accounting</p>
                <a href="enrollmentstats.php?course=ACC201&name=Financial Accounting&faculty=accounting&program=bachelor-accounting" class="view-stats-btn">View Enrollment Stats</a>
            </div>
        </div>
    </div>

    <div class="footer">
        Copyright &copy; 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved.<br>
        Privacy Policy | Terms of Use
    </div>

    <script>
        // Load faculty and program filters
        async function loadFilters() {
            try {
                const response = await fetch('backend/search_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'get_filters'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const facultySelect = document.getElementById('faculty');
                    const programSelect = document.getElementById('program');
                    
                    // Clear existing options except "All"
                    facultySelect.innerHTML = '<option value="all">All Faculties</option>';
                    programSelect.innerHTML = '<option value="all">All Programs</option>';
                    
                    // Add faculty options
                    data.data.faculties.forEach(faculty => {
                        const option = document.createElement('option');
                        option.value = faculty.toLowerCase();
                        option.textContent = faculty.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        facultySelect.appendChild(option);
                    });
                    
                    // Add program options
                    data.data.programs.forEach(program => {
                        const option = document.createElement('option');
                        option.value = program.code;
                        option.textContent = program.name;
                        programSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading filters:', error);
            }
        }

        async function searchCourses() {
            const searchTerm = document.getElementById('courseSearch').value;
            const facultyFilter = document.getElementById('faculty').value;
            const programFilter = document.getElementById('program').value;
            
            try {
                const response = await fetch('backend/search_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        search: searchTerm,
                        faculty: facultyFilter,
                        program: programFilter
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const resultsContainer = document.getElementById('searchResults');
                    resultsContainer.innerHTML = '';
                    
                    data.courses.forEach(course => {
                        const courseElement = document.createElement('div');
                        courseElement.className = 'course-result';
                        courseElement.innerHTML = `
                            <h3>${course.code} - ${course.name}</h3>
                            <p>Teachers: ${course.teachers}</p>
                            <p>${course.program}</p>
                            <a href="enrollmentstats.php?course=${course.code}&name=${encodeURIComponent(course.name)}&faculty=${facultyFilter}&program=${programFilter}" 
                               class="view-stats-btn">View Enrollment Stats</a>
                        `;
                        resultsContainer.appendChild(courseElement);
                    });
                } else {
                    console.error('Search failed:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        // Initialize with all courses visible
        document.addEventListener('DOMContentLoaded', function() {
            loadFilters().then(() => {
                searchCourses();
            });
            
            // Add event listeners for filters
            document.getElementById('faculty').addEventListener('change', searchCourses);
            document.getElementById('program').addEventListener('change', searchCourses);
            document.getElementById('courseSearch').addEventListener('input', searchCourses);
        });
    </script>
</body>
</html>
