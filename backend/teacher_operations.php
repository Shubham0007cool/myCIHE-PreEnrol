<?php
require_once 'db.php';

function getAllUnits() {
    global $conn;
    
    try {
        $sql = "SELECT id, unit_code, unit_name FROM units ORDER BY unit_code";
        $result = $conn->query($sql);
        
        $units = [];
        while ($row = $result->fetch_assoc()) {
            $units[] = $row;
        }
        
        return $units;
        
    } catch (Exception $e) {
        error_log("Error getting units: " . $e->getMessage());
        throw $e;
    }
}

function getAllTeachers() {
    global $conn;
    
    try {
        $sql = "SELECT t.*, GROUP_CONCAT(u.unit_code SEPARATOR ', ') as units 
                FROM teachers t 
                LEFT JOIN units u ON t.id = u.teacher_id 
                GROUP BY t.id 
                ORDER BY t.teacher_id";
        $result = $conn->query($sql);
        
        $teachers = [];
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        
        return $teachers;
        
    } catch (Exception $e) {
        error_log("Error getting teachers: " . $e->getMessage());
        throw $e;
    }
}

function addTeacher($teacherId, $firstName, $lastName, $email, $password, $department, $units) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert teacher
        $sql = "INSERT INTO teachers (teacher_id, first_name, last_name, email, password, department) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssss', $teacherId, $firstName, $lastName, $email, $hashedPassword, $department);
        $stmt->execute();
        
        $teacherId = $conn->insert_id;
        
        // Update unit assignments
        if (!empty($units)) {
            $sql = "UPDATE units SET teacher_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            
            foreach ($units as $unitId) {
                $stmt->bind_param('ii', $teacherId, $unitId);
                $stmt->execute();
            }
        }
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error adding teacher: " . $e->getMessage());
        throw $e;
    }
}

function deleteTeacher($teacherId) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // Remove teacher from units
        $sql = "UPDATE units SET teacher_id = NULL WHERE teacher_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $teacherId);
        $stmt->execute();
        
        // Delete teacher
        $sql = "DELETE FROM teachers WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $teacherId);
        $stmt->execute();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting teacher: " . $e->getMessage());
        throw $e;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_teacher':
                    $teacherId = $_POST['teacher_id'] ?? '';
                    $firstName = $_POST['first_name'] ?? '';
                    $lastName = $_POST['last_name'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $password = $_POST['password'] ?? '';
                    $department = $_POST['department'] ?? '';
                    $units = $_POST['units'] ?? [];
                    
                    if (empty($teacherId) || empty($firstName) || empty($lastName) || 
                        empty($email) || empty($password) || empty($department)) {
                        throw new Exception("Missing required fields");
                    }
                    
                    addTeacher($teacherId, $firstName, $lastName, $email, $password, $department, $units);
                    echo json_encode(['success' => true, 'message' => 'Teacher added successfully']);
                    break;
                    
                case 'delete_teacher':
                    $teacherId = $_POST['teacher_id'] ?? 0;
                    
                    if (empty($teacherId)) {
                        throw new Exception("Missing teacher ID");
                    }
                    
                    deleteTeacher($teacherId);
                    echo json_encode(['success' => true, 'message' => 'Teacher deleted successfully']);
                    break;
                    
                default:
                    throw new Exception("Invalid action");
            }
        } else {
            throw new Exception("No action specified");
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 