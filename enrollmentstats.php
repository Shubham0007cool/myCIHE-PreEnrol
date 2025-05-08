<?php
require_once 'includes/admin_header.php';
require_once 'backend/admin_operations.php';
?>

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
        .it-badge {
            background-color: #3498db;
        }
        .business-badge {
            background-color: #2ecc71;
        }
        .accounting-badge {
            background-color: #9b59b6;
        }
        .enrollment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
            margin-top: 20px;
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
    </style>
    

    <div class="enrollment-container">
        <div class="course-header">
            <h2 id="course-title">Enrollment Statistics</h2>
            <p id="course-description"></p>
        </div>
        
        <table class="enrollment-table">
            <thead>
                <tr>
                    <th>Days</th>
                    <th>No. of students</th>
                    <th>8:30am-11:30am</th>
                    <th>12:00pm-3:00pm</th>
                    <th>3:00pm-6:00pm</th>
                    <th>6:00pm-9:00pm</th>
                </tr>
            </thead>
            <tbody id="stats-body">
                <!-- Data will be inserted here by JavaScript -->
            </tbody>
        </table>

        <div class="export-controls">
            <button id="export-excel" class="export-btn">Export to Excel</button>
        </div>
    </div>

    <div class="footer">
        Copyright &copy; 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved.<br>
        Privacy Policy | Terms of Use
    </div>

    <script>
        // Get course info from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const courseCode = urlParams.get('course') || 'ICT305';
        const courseName = urlParams.get('name') || 'Topics in IT';
        const faculty = urlParams.get('faculty') || 'it';
        const program = urlParams.get('program') || 'bachelor-it';

        // Set the page title and header
        document.title = `${courseCode} - ${courseName} Enrollment Stats`;
        document.getElementById('course-title').textContent = `${courseCode} - ${courseName} Enrollment Statistics`;
        
        // Set faculty badge
        let facultyBadge = '';
        let facultyName = '';
        switch(faculty) {
            case 'it':
                facultyBadge = '<span class="faculty-badge it-badge">IT</span>';
                facultyName = 'Faculty of Information Technology';
                break;
            case 'business':
                facultyBadge = '<span class="faculty-badge business-badge">Business</span>';
                facultyName = 'Business Faculty';
                break;
            case 'accounting':
                facultyBadge = '<span class="faculty-badge accounting-badge">Accounting</span>';
                facultyName = 'Accounting Faculty';
                break;
        }
        
        document.getElementById('course-description').innerHTML = `
            ${program.replace('-', ' ').toUpperCase()} ${facultyBadge}<br>
            ${facultyName}
        `;

        // Display the enrollment data
        async function displayEnrollmentData() {
            try {
                const response = await fetch(`backend/enrollment_operations.php?course=${courseCode}`);
                const data = await response.json();
                
                if (data.success) {
                    const stats = data.data;
                    const tbody = document.getElementById('stats-body');
                    tbody.innerHTML = '';
                    
                    stats.enrollment_data.forEach(row => {
                        const tr = document.createElement('tr');
                        if (row.isTotal) {
                            tr.className = 'total-row';
                            tr.innerHTML = `
                                <td><strong>Total students</strong></td>
                                <td><strong>${row.total}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            `;
                        } else {
                            tr.innerHTML = `
                                <td>${row.day}</td>
                                <td>${row.total}</td>
                                <td>${row.morning}</td>
                                <td>${row.midday}</td>
                                <td>${row.afternoon}</td>
                                <td>${row.evening}</td>
                            `;
                        }
                        tbody.appendChild(tr);
                    });
                } else {
                    console.error('Failed to load enrollment stats:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Export to Excel functionality
        document.getElementById('export-excel').addEventListener('click', exportToExcel);
        
        function exportToExcel() {
            // Get the course details
            const courseCode = new URLSearchParams(window.location.search).get('course') || 'ICT305';
            const courseName = new URLSearchParams(window.location.search).get('name') || 'Topics in IT';
            
            // Create worksheet data
            const wsData = [
                [`Course: ${courseCode} - ${courseName}`],
                [`Faculty: ${facultyName}`],
                [`Program: ${program.replace('-', ' ').toUpperCase()}`],
                [],
                ["Day", "Total Students", "8:30am-11:30am", "12:00pm-3:00pm", "3:00pm-6:00pm", "6:00pm-9:00pm"]
            ];
            
            // Add table data
            const tbody = document.getElementById('stats-body');
            const rows = tbody.getElementsByTagName('tr');
            
            for (let row of rows) {
                const cells = row.getElementsByTagName('td');
                const rowData = [];
                for (let cell of cells) {
                    rowData.push(cell.textContent.trim());
                }
                wsData.push(rowData);
            }
            
            // Create workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            
            // Set column widths
            ws['!cols'] = [
                {wch: 15}, // Day
                {wch: 15}, // Total Students
                {wch: 15}, // Morning
                {wch: 15}, // Midday
                {wch: 15}, // Afternoon
                {wch: 15}  // Evening
            ];
            
            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Enrollment Stats");
            
            // Export
            XLSX.writeFile(wb, `${courseCode}_enrollment.xlsx`);
        }

        // Initialize the page
        displayEnrollmentData();
    </script>
</body>
</html>
