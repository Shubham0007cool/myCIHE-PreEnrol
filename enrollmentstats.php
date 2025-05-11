<?php
require_once 'includes/admin_header.php';
require_once 'backend/admin_operations.php';
require_once 'backend/db.php';

// Get all courses with their program information
$courses_sql = "SELECT 
    c.course_code,
    c.course_name,
    p.program_name,
    p.program_type,
    t.department as faculty_name,
    CASE 
        WHEN t.department = 'IT' THEN 'IT'
        WHEN t.department = 'Business' THEN 'BUS'
        WHEN t.department = 'Accounting' THEN 'ACC'
        ELSE 'IT'
    END as faculty_code
FROM courses c
JOIN programs p ON c.program_id = p.id
LEFT JOIN units u ON u.course_id = c.id
LEFT JOIN teachers t ON u.teacher_id = t.id
GROUP BY c.course_code, c.course_name, p.program_name, p.program_type, t.department
ORDER BY t.department, c.course_code";

$courses_result = mysqli_query($conn, $courses_sql);
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);
?>

<!-- Add XLSX library -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
    .course-header {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #3498db;
    }
    .course-header h2 {
        color: #2c3e50;
        margin: 0 0 5px 0;
    }
    .course-header p {
        margin: 0;
        color: #7f8c8d;
    }
    .faculty-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        margin-left: 10px;
    }
    .it-badge { background-color: #3498db; }
    .business-badge { background-color: #2ecc71; }
    .accounting-badge { background-color: #9b59b6; }
    .enrollment-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-bottom: 40px;
    }
    .enrollment-table th {
        background-color: #f2f2f2;
        text-align: center;
        padding: 12px;
        border: 1px solid #ddd;
    }
    .enrollment-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }
    .enrollment-table tr:hover {
        background-color: #f5f5f5;
    }
    .total-row {
        font-weight: bold;
        background-color: #f2f2f2;
    }
    .total-row td {
        border-top: 2px solid #333;
    }
    .export-controls {
        margin: 20px 0;
        text-align: right;
    }
    .export-btn {
        padding: 10px 15px;
        background-color: #2ecc71;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    .export-btn:hover {
        background-color: #27ae60;
    }
    .course-section {
        margin-bottom: 40px;
    }
    .course-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .course-title h3 {
        margin: 0;
    }
    .course-export {
        margin-left: 20px;
    }
    .program-type {
        font-size: 0.9em;
        color: #666;
        margin-top: 5px;
    }
    .status-count {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.9em;
        margin: 0 2px;
    }
    .status-pending { background-color: #f1c40f; color: #000; }
    .status-approved { background-color: #2ecc71; color: #fff; }
    .status-rejected { background-color: #e74c3c; color: #fff; }
</style>

<div class="enrollment-container">
    <div class="course-header">
        <h2>Enrollment Statistics Overview</h2>
        <p>Comprehensive view of all course enrollments</p>
    </div>
    
    <div class="export-controls">
        <button id="export-excel" class="export-btn">Export All to Excel</button>
    </div>

    <div id="courses-container">
        <!-- Course tables will be inserted here by JavaScript -->
    </div>
</div>

<div class="footer">
    Copyright &copy; 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved.<br>
    Privacy Policy | Terms of Use
</div>

<script>
    // Store courses data globally
    const courses = <?php echo json_encode($courses); ?>;

    // Get faculty badge HTML
    function getFacultyBadge(facultyCode) {
        const badges = {
            'IT': 'it-badge',
            'BUS': 'business-badge',
            'ACC': 'accounting-badge'
        };
        const badgeClass = badges[facultyCode] || 'it-badge';
        return `<span class="faculty-badge ${badgeClass}">${facultyCode}</span>`;
    }

    // Format program type
    function formatProgramType(type) {
        return type.charAt(0).toUpperCase() + type.slice(1);
    }

    // Create course table HTML
    function createCourseTable(course) {
        return `
            <div class="course-section" id="course-${course.course_code}">
                <div class="course-title">
                    <div>
                        <h3>${course.course_code} - ${course.course_name}</h3>
                        <p>
                            ${course.program_name} ${getFacultyBadge(course.faculty_code)}<br>
                            ${course.faculty_name || 'Department Not Assigned'}
                            <div class="program-type">${formatProgramType(course.program_type)} Program</div>
                        </p>
                    </div>
                    <button class="export-btn course-export" onclick="exportCourseToExcel('${course.course_code}')">
                        Export to Excel
                    </button>
                </div>
                <table class="enrollment-table">
                    <thead>
                        <tr>
                            <th>Days</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>8:30am-11:30am</th>
                            <th>12:00pm-3:00pm</th>
                            <th>3:00pm-6:00pm</th>
                            <th>6:00pm-9:00pm</th>
                        </tr>
                    </thead>
                    <tbody id="stats-${course.course_code}">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
            </div>
        `;
    }

    // Display enrollment data for a course
    async function displayCourseData(courseCode) {
        try {
            const response = await fetch(`backend/enrollment_operations.php?course=${courseCode}`);
            const data = await response.json();
            
            if (data.success) {
                const stats = data.data;
                const tbody = document.getElementById(`stats-${courseCode}`);
                tbody.innerHTML = '';
                
                stats.enrollment_data.forEach(row => {
                    const tr = document.createElement('tr');
                    if (row.isTotal) {
                        tr.className = 'total-row';
                        tr.innerHTML = `
                            <td><strong>Total</strong></td>
                            <td><strong>${row.total}</strong></td>
                            <td>
                                <span class="status-count status-pending">${row.pending} Pending</span>
                                <span class="status-count status-approved">${row.approved} Approved</span>
                                <span class="status-count status-rejected">${row.rejected} Rejected</span>
                            </td>
                            <td><strong>${row.morning}</strong></td>
                            <td><strong>${row.midday}</strong></td>
                            <td><strong>${row.afternoon}</strong></td>
                            <td><strong>${row.evening}</strong></td>
                        `;
                    } else {
                        tr.innerHTML = `
                            <td>${row.day}</td>
                            <td>${row.total}</td>
                            <td>
                                <span class="status-count status-pending">${row.pending} Pending</span>
                                <span class="status-count status-approved">${row.approved} Approved</span>
                                <span class="status-count status-rejected">${row.rejected} Rejected</span>
                            </td>
                            <td>${row.morning}</td>
                            <td>${row.midday}</td>
                            <td>${row.afternoon}</td>
                            <td>${row.evening}</td>
                        `;
                    }
                    tbody.appendChild(tr);
                });
            }
        } catch (error) {
            console.error(`Error loading data for ${courseCode}:`, error);
        }
    }

    // Export individual course to Excel
    function exportCourseToExcel(courseCode) {
        const course = courses.find(c => c.course_code === courseCode);
        if (!course) {
            console.error('Course not found:', courseCode);
            return;
        }

        const tbody = document.getElementById(`stats-${courseCode}`);
        if (!tbody) {
            console.error('Table body not found for course:', courseCode);
            return;
        }

        const rows = tbody.getElementsByTagName('tr');
        const wsData = [
            [`Course: ${course.course_code} - ${course.course_name}`],
            [`Program: ${course.program_name} (${formatProgramType(course.program_type)})`],
            [`Department: ${course.faculty_name || 'Not Assigned'}`],
            [],
            ['Day', 'Total', 'Pending', 'Approved', 'Rejected', '8:30am-11:30am', '12:00pm-3:00pm', '3:00pm-6:00pm', '6:00pm-9:00pm']
        ];
        
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            const rowData = [];
            for (let cell of cells) {
                if (cell.querySelector('.status-count')) {
                    const spans = cell.querySelectorAll('.status-count');
                    spans.forEach(span => {
                        const text = span.textContent.trim();
                        const number = parseInt(text);
                        rowData.push(isNaN(number) ? text : number);
                    });
                } else {
                    rowData.push(cell.textContent.trim());
                }
            }
            wsData.push(rowData);
        }
        
        try {
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            
            // Set column widths
            ws['!cols'] = [
                {wch: 15}, // Day
                {wch: 10}, // Total
                {wch: 10}, // Pending
                {wch: 10}, // Approved
                {wch: 10}, // Rejected
                {wch: 15}, // Morning
                {wch: 15}, // Midday
                {wch: 15}, // Afternoon
                {wch: 15}  // Evening
            ];
            
            XLSX.utils.book_append_sheet(wb, ws, courseCode);
            XLSX.writeFile(wb, `${courseCode}_enrollment.xlsx`);
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Error exporting to Excel. Please try again.');
        }
    }

    // Export all courses to Excel
    document.getElementById('export-excel').addEventListener('click', function() {
        try {
            const wb = XLSX.utils.book_new();
            
            // Create a worksheet for each course
            courses.forEach(course => {
                const tbody = document.getElementById(`stats-${course.course_code}`);
                if (!tbody) {
                    console.warn(`Table not found for course: ${course.course_code}`);
                    return;
                }

                const rows = tbody.getElementsByTagName('tr');
                if (!rows || rows.length === 0) {
                    console.warn(`No data found for course: ${course.course_code}`);
                    return;
                }

                const wsData = [
                    [`Course: ${course.course_code} - ${course.course_name}`],
                    [`Program: ${course.program_name} (${formatProgramType(course.program_type)})`],
                    [`Department: ${course.faculty_name || 'Not Assigned'}`],
                    [],
                    ['Day', 'Total', 'Pending', 'Approved', 'Rejected', '8:30am-11:30am', '12:00pm-3:00pm', '3:00pm-6:00pm', '6:00pm-9:00pm']
                ];
                
                for (let row of rows) {
                    const cells = row.getElementsByTagName('td');
                    const rowData = [];
                    
                    for (let cell of cells) {
                        if (cell.querySelector('.status-count')) {
                            const spans = cell.querySelectorAll('.status-count');
                            spans.forEach(span => {
                                const text = span.textContent.trim();
                                const number = parseInt(text);
                                rowData.push(isNaN(number) ? text : number);
                            });
                        } else {
                            const text = cell.textContent.trim();
                            // Try to parse as number if it's not a day name
                            if (!['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'Total'].includes(text)) {
                                const number = parseInt(text);
                                rowData.push(isNaN(number) ? text : number);
                            } else {
                                rowData.push(text);
                            }
                        }
                    }
                    wsData.push(rowData);
                }
                
                try {
                    const ws = XLSX.utils.aoa_to_sheet(wsData);
                    
                    // Set column widths
                    ws['!cols'] = [
                        {wch: 15}, // Day
                        {wch: 10}, // Total
                        {wch: 10}, // Pending
                        {wch: 10}, // Approved
                        {wch: 10}, // Rejected
                        {wch: 15}, // Morning
                        {wch: 15}, // Midday
                        {wch: 15}, // Afternoon
                        {wch: 15}  // Evening
                    ];
                    
                    XLSX.utils.book_append_sheet(wb, ws, course.course_code);
                } catch (sheetError) {
                    console.error(`Error creating sheet for ${course.course_code}:`, sheetError);
                }
            });
            
            // Check if any sheets were added
            if (wb.SheetNames.length === 0) {
                throw new Error('No data available to export');
            }
            
            // Export
            XLSX.writeFile(wb, 'enrollment_statistics.xlsx');
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Error exporting to Excel: ' + error.message);
        }
    });

    // Initialize the page
    async function initializePage() {
        const container = document.getElementById('courses-container');
        
        // Create tables for each course
        courses.forEach(course => {
            container.innerHTML += createCourseTable(course);
        });
        
        // Load data for each course
        for (const course of courses) {
            await displayCourseData(course.course_code);
        }
    }

    // Initialize the page
    initializePage();
</script>
</body>
</html>
