<?php
require_once 'db.php';

function getEnrollmentStats($course_code) {
    global $conn;
    
    try {
        // Get course details
        $course_sql = "SELECT 
                        c.course_code,
                        c.course_name,
                        p.program_name,
                        p.program_type
                    FROM courses c
                    LEFT JOIN programs p ON c.program_id = p.id
                    WHERE c.course_code = ?";
        
        $course_stmt = $conn->prepare($course_sql);
        $course_stmt->bind_param("s", $course_code);
        $course_stmt->execute();
        $course_result = $course_stmt->get_result();
        
        if ($course_result->num_rows === 0) {
            throw new Exception("Course not found");
        }
        
        $course = $course_result->fetch_assoc();
        
        // Get enrollment statistics by day and time slot
        $stats_sql = "SELECT 
                        DAYNAME(e.enrollment_date) as day,
                        COUNT(DISTINCT e.student_id) as total,
                        SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '08:30:00' AND '11:30:00' THEN 1 ELSE 0 END) as morning,
                        SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '12:00:00' AND '15:00:00' THEN 1 ELSE 0 END) as midday,
                        SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '15:00:00' AND '18:00:00' THEN 1 ELSE 0 END) as afternoon,
                        SUM(CASE WHEN TIME(e.enrollment_date) BETWEEN '18:00:00' AND '21:00:00' THEN 1 ELSE 0 END) as evening
                    FROM enrollments e
                    JOIN units u ON e.unit_id = u.id
                    WHERE u.course_id = (SELECT id FROM courses WHERE course_code = ?)
                    GROUP BY DAYNAME(e.enrollment_date)
                    ORDER BY FIELD(DAYNAME(e.enrollment_date), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')";
        
        $stats_stmt = $conn->prepare($stats_sql);
        $stats_stmt->bind_param("s", $course_code);
        $stats_stmt->execute();
        $stats_result = $stats_stmt->get_result();
        
        $enrollment_data = [];
        $total_students = 0;
        
        while ($row = $stats_result->fetch_assoc()) {
            $enrollment_data[] = [
                'day' => $row['day'],
                'total' => (int)$row['total'],
                'morning' => (int)$row['morning'],
                'midday' => (int)$row['midday'],
                'afternoon' => (int)$row['afternoon'],
                'evening' => (int)$row['evening']
            ];
            $total_students += $row['total'];
        }
        
        // Add total row
        $enrollment_data[] = [
            'isTotal' => true,
            'total' => $total_students
        ];
        
        return [
            'course' => $course,
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
        if (!isset($_GET['course'])) {
            throw new Exception('Course code is required');
        }
        
        $stats = getEnrollmentStats($_GET['course']);
        echo json_encode(['success' => true, 'data' => $stats]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 