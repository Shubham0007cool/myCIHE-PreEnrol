<?php
require_once 'db.php';

function getAllCourses() {
    global $conn;
    $sql = "SELECT c.*, p.program_name 
            FROM courses c 
            LEFT JOIN programs p ON c.program_id = p.id 
            ORDER BY c.course_code";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllPrograms() {
    global $conn;
    $sql = "SELECT * FROM programs ORDER BY program_code";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addCourse($data) {
    global $conn;
    
    // Validate required fields
    if (empty($data['course_code']) || empty($data['course_name']) || 
        empty($data['program_id']) || empty($data['credits'])) {
        return ['success' => false, 'message' => 'All required fields must be filled'];
    }
    
    // Check if course code already exists
    $stmt = $conn->prepare("SELECT id FROM courses WHERE course_code = ?");
    $stmt->bind_param("s", $data['course_code']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Course code already exists'];
    }
    
    // Insert new course
    $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, description, credits, program_id, image_path) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiis", 
        $data['course_code'],
        $data['course_name'],
        $data['description'],
        $data['credits'],
        $data['program_id'],
        $data['image_path']
    );
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Course added successfully'];
    } else {
        return ['success' => false, 'message' => 'Error adding course: ' . $conn->error];
    }
}

function deleteCourse($courseId) {
    global $conn;
    
    // Check if course exists
    $stmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        return ['success' => false, 'message' => 'Course not found'];
    }
    
    // Delete course
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Course deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Error deleting course: ' . $conn->error];
    }
}

function getCourses() {
    global $conn;
    
    $sql = "SELECT c.*, p.program_name 
            FROM courses c 
            JOIN programs p ON c.program_id = p.id 
            ORDER BY p.program_name";
            
    $result = $conn->query($sql);
    $courses = [];
    
    while ($row = $result->fetch_assoc()) {
        $courses[] = [
            'id' => $row['id'],
            'code' => $row['course_code'],
            'name' => $row['course_name'],
            'description' => $row['description'],
            'credits' => $row['credits'],
            'program_name' => $row['program_name'],
            'image_path' => $row['image_path']
        ];
    }
    
    return $courses;
}

function getUnitsByCourse($course_id) {
    global $conn;
    
    $sql = "SELECT u.*, t.first_name as teacher_first_name, t.last_name as teacher_last_name 
            FROM units u 
            LEFT JOIN teachers t ON u.teacher_id = t.id 
            WHERE u.course_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $units = [];
    while ($row = $result->fetch_assoc()) {
        $units[] = [
            'id' => $row['id'],
            'code' => $row['unit_code'],
            'name' => $row['unit_name'],
            'description' => $row['description'],
            'credits' => $row['credits'],
            'teacher_name' => $row['teacher_first_name'] ? 
                $row['teacher_first_name'] . ' ' . $row['teacher_last_name'] : 'Not Assigned'
        ];
    }
    
    return $units;
}

function getUnitsByCourseCode($course_code) {
    global $conn;
    
    $sql = "SELECT u.*, c.course_name, c.course_code, t.id as teacher_id, t.first_name, t.last_name 
            FROM units u 
            JOIN courses c ON u.course_id = c.id 
            LEFT JOIN teachers t ON u.teacher_id = t.id 
            WHERE c.course_code = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $units = [];
    while ($row = $result->fetch_assoc()) {
        $units[] = [
            'id' => $row['id'],
            'code' => $row['unit_code'],
            'name' => $row['unit_name'],
            'description' => $row['description'],
            'credits' => $row['credits'],
            'course_name' => $row['course_name'],
            'course_code' => $row['course_code'],
            'teacher_id' => $row['teacher_id'],
            'teacher_name' => $row['first_name'] ? 
                $row['first_name'] . ' ' . $row['last_name'] : 'Not Assigned'
        ];
    }
    
    return $units;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_course':
            $result = addCourse($_POST);
            echo json_encode($result);
            break;
            
        case 'delete_course':
            $courseId = $_POST['course_id'] ?? 0;
            $result = deleteCourse($courseId);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action'] ?? '') {
        case 'get_courses':
            echo json_encode(getAllCourses());
            break;
            
        case 'get_units':
            if (isset($_GET['course_code'])) {
                echo json_encode(getUnitsByCourseCode($_GET['course_code']));
            } else {
                echo json_encode(['error' => 'Course code required']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}
?> 