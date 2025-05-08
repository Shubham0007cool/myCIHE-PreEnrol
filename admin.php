<?php
require_once 'includes/admin_header.php';
require_once 'backend/admin_operations.php';

// Get dashboard statistics
$stats = getDashboardStats();
$recent_enrollments = getRecentEnrollments();
?>

<div class="main">
    <div class="content-container">
        <div class="text-box">
            <h2>Admin Dashboard</h2>
            <p>Crown Institute of Higher Education (CIHE) has a long-term vision to become a provider of choice for students who wish to gain an internationally oriented business education of high quality.</p>
            <p>Crown understands that to support the success of our students and to develop as a high-quality higher education institution, we must foster an engaging and supportive educational environment.</p>
        </div>
        <div class="image-box">
            <img src="girllogo.png" alt="Student Image">
        </div>
    </div>

    <div class="dashboard-overview">
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Pre-Enrollments</h3>
                <p class="stat-value"><?php echo number_format($stats['total_enrollments']); ?></p>
                <p class="stat-change">↑ 12% from last period</p>
            </div>
            <div class="stat-card">
                <h3>Pending Approvals</h3>
                <p class="stat-value"><?php echo number_format($stats['pending_approvals']); ?></p>
                <a href="search.php" class="action-link">See Pre-enrollments stats</a>
            </div>
            <div class="stat-card">
                <h3>Most Popular Course</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['popular_course']); ?></p>
                <p class="stat-detail"><?php echo number_format($stats['popular_course_count']); ?> enrollments</p>
            </div>
            <div class="stat-card">
                <h3>Enrollment Period</h3>
                <p class="stat-value"><?php echo htmlspecialchars($stats['enrollment_period']); ?></p>
                <p class="stat-detail">Closes: <?php echo htmlspecialchars($stats['closing_date']); ?></p>
            </div>
        </div>

        <div class="activity-section">
            <h2>Recent Pre-Enrollments</h2>
            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Faculty</th>
                            <th>Courses Enrolled</th>
                            <th>Enrollment Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_enrollments as $enrollment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($enrollment['student_id']); ?></td>
                            <td>
                                <span class="enrollment-faculty faculty-<?php 
                                    echo strtolower(str_replace(' ', '-', $enrollment['faculty'])); 
                                ?>">
                                    <?php echo htmlspecialchars($enrollment['faculty']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($enrollment['courses']); ?></td>
                            <td><span class="enrollment-time"><?php echo htmlspecialchars($enrollment['enrollment_time']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <a href="search.php" class="view-all-link">View All Pre-Enrollments →</a>
        </div>
    </div>
</div>

<div class="footer">
    Copyright &copy; 2007 - 2024 WebSutra Technology Pty Ltd Trading as CIHE Project Team. All Rights Reserved.<br>
    Privacy Policy | Terms of Use
</div>

<script>
// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    fetch('backend/admin_operations.php?action=get_stats')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.stat-card:nth-child(1) .stat-value').textContent = 
                new Intl.NumberFormat().format(data.total_enrollments);
            document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = 
                new Intl.NumberFormat().format(data.pending_approvals);
            document.querySelector('.stat-card:nth-child(3) .stat-value').textContent = 
                data.popular_course;
            document.querySelector('.stat-card:nth-child(3) .stat-detail').textContent = 
                new Intl.NumberFormat().format(data.popular_course_count) + ' enrollments';
            document.querySelector('.stat-card:nth-child(4) .stat-value').textContent = 
                data.enrollment_period;
            document.querySelector('.stat-card:nth-child(4) .stat-detail').textContent = 
                'Closes: ' + data.closing_date;
        });

    fetch('backend/admin_operations.php?action=get_recent_enrollments')
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('.activity-table tbody');
            tbody.innerHTML = '';
            
            data.forEach(enrollment => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${enrollment.student_name}</td>
                    <td>${enrollment.student_id}</td>
                    <td><span class="enrollment-faculty faculty-${enrollment.faculty.toLowerCase().replace(' ', '-')}">${enrollment.faculty}</span></td>
                    <td>${enrollment.courses}</td>
                    <td><span class="enrollment-time">${enrollment.enrollment_time}</span></td>
                `;
                tbody.appendChild(tr);
            });
        });
}, 300000); // 5 minutes
</script>
</body>
</html>
