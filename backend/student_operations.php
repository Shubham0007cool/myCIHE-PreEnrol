<?php
require_once 'db.php';

function searchStudents($searchTerm = '') {
    global $conn;
    
    try {
        $sql = "SELECT 
                    s.id,
                    s.student_id,
                    s.first_name,
                    s.last_name,
                    s.email,
                    s.contact_number,
                    s.emergency_contact,
                    p.program_name,
                    GROUP_CONCAT(DISTINCT CONCAT(u.unit_code, ' - ', u.unit_name) SEPARATOR ', ') as enrolled_units
                FROM students s
                LEFT JOIN programs p ON s.program_id = p.id
                LEFT JOIN enrollments e ON s.id = e.student_id
                LEFT JOIN units u ON e.unit_id = u.id
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($searchTerm)) {
            $sql .= " AND (CONCAT(s.first_name, ' ', s.last_name) LIKE ? OR s.student_id LIKE ?)";
            $searchTerm = "%$searchTerm%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        $sql .= " GROUP BY s.id, s.student_id, s.first_name, s.last_name, s.email, s.contact_number, s.emergency_contact, p.program_name";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'id' => $row['id'],
                'student_id' => $row['student_id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['contact_number'],
                'emergency_contact' => $row['emergency_contact'],
                'program' => $row['program_name'],
                'courses' => $row['enrolled_units'] ? explode(', ', $row['enrolled_units']) : []
            ];
        }
        
        return $students;
        
    } catch (Exception $e) {
        error_log("Error searching students: " . $e->getMessage());
        throw $e;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $searchTerm = $_POST['search'] ?? '';
        $students = searchStudents($searchTerm);
        echo json_encode(['success' => true, 'students' => $students]);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 