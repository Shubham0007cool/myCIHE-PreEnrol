<?php
require_once 'db.php';

function getFacultiesAndPrograms() {
    global $conn;
    
    try {
        // Get all programs with their faculty types
        $sql = "SELECT DISTINCT 
                    p.program_type as faculty,
                    p.program_name,
                    p.program_code
                FROM programs p
                ORDER BY p.program_type, p.program_name";
        
        $result = $conn->query($sql);
        
        $faculties = [];
        $programs = [];
        
        while ($row = $result->fetch_assoc()) {
            // Add faculty if not exists
            if (!in_array($row['faculty'], $faculties)) {
                $faculties[] = $row['faculty'];
            }
            
            // Add program
            $programs[] = [
                'code' => $row['program_code'],
                'name' => $row['program_name'],
                'faculty' => $row['faculty']
            ];
        }
        
        return [
            'faculties' => $faculties,
            'programs' => $programs
        ];
        
    } catch (Exception $e) {
        error_log("Error getting faculties and programs: " . $e->getMessage());
        throw $e;
    }
}

function searchCourses($searchTerm = '', $faculty = 'all', $program = 'all') {
    global $conn;
    
    try {
        $sql = "SELECT 
                    c.course_code,
                    c.course_name,
                    p.program_name,
                    GROUP_CONCAT(DISTINCT CONCAT(t.first_name, ' ', t.last_name) SEPARATOR ', ') as teachers,
                    c.id as course_id
                FROM courses c
                LEFT JOIN programs p ON c.program_id = p.id
                LEFT JOIN units u ON u.course_id = c.id
                LEFT JOIN teachers t ON u.teacher_id = t.id
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($searchTerm)) {
            $sql .= " AND (c.course_code LIKE ? OR c.course_name LIKE ?)";
            $searchTerm = "%$searchTerm%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        if ($faculty !== 'all') {
            $sql .= " AND p.program_type = ?";
            $params[] = $faculty;
            $types .= "s";
        }
        
        if ($program !== 'all') {
            $sql .= " AND p.program_code = ?";
            $params[] = $program;
            $types .= "s";
        }
        
        $sql .= " GROUP BY c.id, c.course_code, c.course_name, p.program_name";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = [
                'code' => $row['course_code'],
                'name' => $row['course_name'],
                'program' => $row['program_name'],
                'teachers' => $row['teachers'] ?: 'No teachers assigned',
                'course_id' => $row['course_id']
            ];
        }
        
        return $courses;
        
    } catch (Exception $e) {
        error_log("Error searching courses: " . $e->getMessage());
        throw $e;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'get_filters') {
            $filters = getFacultiesAndPrograms();
            echo json_encode(['success' => true, 'data' => $filters]);
        } else {
            $searchTerm = $_POST['search'] ?? '';
            $faculty = $_POST['faculty'] ?? 'all';
            $program = $_POST['program'] ?? 'all';
            
            $courses = searchCourses($searchTerm, $faculty, $program);
            echo json_encode(['success' => true, 'courses' => $courses]);
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 