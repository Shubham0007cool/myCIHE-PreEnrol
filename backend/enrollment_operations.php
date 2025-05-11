<?php
require_once 'db.php';

function getEnrollmentStats($course_code = null) {
    global $conn;
    
    try {
        // Get enrollment statistics by course, day and time slot
        $stats_sql = "SELECT 
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
            END as faculty_code,
            DAYNAME(e.enrollment_date) as day,
            COUNT(DISTINCT e.student_id) as total,
            SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '08:30:00' AND '11:30:00' THEN 1 ELSE 0 END) as morning,
            SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '12:00:00' AND '15:00:00' THEN 1 ELSE 0 END) as midday,
            SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '15:00:00' AND '18:00:00' THEN 1 ELSE 0 END) as afternoon,
            SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '18:00:00' AND '21:00:00' THEN 1 ELSE 0 END) as evening,
            SUM(CASE WHEN e.status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN e.status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN e.status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM enrollments e
        JOIN units u ON e.unit_id = u.id
        JOIN courses c ON u.course_id = c.id
        JOIN programs p ON c.program_id = p.id
        LEFT JOIN teachers t ON u.teacher_id = t.id
        WHERE e.semester = (SELECT MAX(semester) FROM enrollments)";
        
        $params = [];
        $types = "";
        
        if ($course_code) {
            $stats_sql .= " AND c.course_code = ?";
            $params[] = $course_code;
            $types .= "s";
        }
        
        $stats_sql .= " GROUP BY c.course_code, c.course_name, p.program_name, p.program_type, 
                       t.department, DAYNAME(e.enrollment_date)
                       ORDER BY t.department, c.course_code, 
                       FIELD(DAYNAME(e.enrollment_date), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')";
        
        $stats_stmt = $conn->prepare($stats_sql);
        
        if (!empty($params)) {
            $stats_stmt->bind_param($types, ...$params);
        }
        
        $stats_stmt->execute();
        $stats_result = $stats_stmt->get_result();
        
        $enrollment_data = [];
        $course_totals = [];
        
        while ($row = $stats_result->fetch_assoc()) {
            $course_key = $row['course_code'];
            
            if (!isset($course_totals[$course_key])) {
                $course_totals[$course_key] = [
                    'total' => 0,
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                    'morning' => 0,
                    'midday' => 0,
                    'afternoon' => 0,
                    'evening' => 0
                ];
            }
            
            $enrollment_data[] = [
                'course_code' => $row['course_code'],
                'course_name' => $row['course_name'],
                'program_name' => $row['program_name'],
                'program_type' => $row['program_type'],
                'faculty_name' => $row['faculty_name'],
                'faculty_code' => $row['faculty_code'],
                'day' => $row['day'],
                'total' => (int)$row['total'],
                'morning' => (int)$row['morning'],
                'midday' => (int)$row['midday'],
                'afternoon' => (int)$row['afternoon'],
                'evening' => (int)$row['evening'],
                'pending' => (int)$row['pending'],
                'approved' => (int)$row['approved'],
                'rejected' => (int)$row['rejected']
            ];
            
            $course_totals[$course_key]['total'] += $row['total'];
            $course_totals[$course_key]['pending'] += $row['pending'];
            $course_totals[$course_key]['approved'] += $row['approved'];
            $course_totals[$course_key]['rejected'] += $row['rejected'];
            $course_totals[$course_key]['morning'] += $row['morning'];
            $course_totals[$course_key]['midday'] += $row['midday'];
            $course_totals[$course_key]['afternoon'] += $row['afternoon'];
            $course_totals[$course_key]['evening'] += $row['evening'];
        }
        
        // Add total rows for each course
        foreach ($course_totals as $code => $totals) {
            $enrollment_data[] = [
                'isTotal' => true,
                'course_code' => $code,
                'total' => $totals['total'],
                'pending' => $totals['pending'],
                'approved' => $totals['approved'],
                'rejected' => $totals['rejected'],
                'morning' => $totals['morning'],
                'midday' => $totals['midday'],
                'afternoon' => $totals['afternoon'],
                'evening' => $totals['evening']
            ];
        }
        
        return [
            'enrollment_data' => $enrollment_data
        ];
        
    } catch (Exception $e) {
        error_log("Error getting enrollment stats: " . $e->getMessage());
        throw $e;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    try {
        $course_code = $_GET['course'] ?? null;
        $stats = getEnrollmentStats($course_code);
        echo json_encode(['success' => true, 'data' => $stats]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 