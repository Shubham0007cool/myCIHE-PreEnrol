<?php
require_once 'db.php';


if (!isset($_SESSION["student_id"])) {
    session_start();
}

function getAvailableUnits($program_id = null) {
    global $conn;
    try {
        $sql = "
            SELECT 
                u.*,
                c.course_name,
                c.course_code,
                t.first_name as teacher_first_name,
                t.last_name as teacher_last_name,
                t.id as teacher_id
            FROM units u
            JOIN courses c ON u.course_id = c.id
            LEFT JOIN teachers t ON u.teacher_id = t.id
        ";
        
        if ($program_id) {
            $sql .= " WHERE c.program_id = ?";
        }
        
        $sql .= " ORDER BY u.unit_code";
        
        $stmt = $conn->prepare($sql);
        
        if ($program_id) {
            $stmt->bind_param("i", $program_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $units = [];
        while ($row = $result->fetch_assoc()) {
            $units[] = $row;
        }
        return $units;
    } catch (Exception $e) {
        error_log("Error fetching available units: " . $e->getMessage());
        return [];
    }
}

function getTeachersForUnit($unit_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT 
                t.id,
                t.first_name,
                t.last_name,
                t.email
            FROM teachers t
            JOIN units u ON u.teacher_id = t.id
            WHERE u.id = ?
        ");
        
        $stmt->bind_param("i", $unit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $teachers = [];
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        return $teachers;
    } catch (Exception $e) {
        error_log("Error fetching teachers: " . $e->getMessage());
        return [];
    }
}

function getCurrentEnrollmentCount($student_id) {
    global $conn;
    try {
        // Get student's internal ID
        $student_sql = "SELECT id FROM students WHERE student_id = ?";
        $student_stmt = $conn->prepare($student_sql);
        $student_stmt->bind_param("s", $student_id);
        $student_stmt->execute();
        $student_result = $student_stmt->get_result();
        
        if ($student_result->num_rows === 0) {
            throw new Exception("Student not found");
        }
        
        $student_row = $student_result->fetch_assoc();
        $internal_student_id = $student_row['id'];
        
        // Get current semester
        $current_semester = date('Y') . (date('n') <= 6 ? '1' : '2');
        
        // Count current enrollments
        $count_sql = "SELECT COUNT(DISTINCT unit_id) as count 
                     FROM enrollments 
                     WHERE student_id = ? AND semester = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("is", $internal_student_id, $current_semester);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        
        return $count_row['count'];
    } catch (Exception $e) {
        error_log("Error getting enrollment count: " . $e->getMessage());
        throw $e;
    }
}

function registerUnits($student_id, $units_data) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check current enrollment count
        $current_count = getCurrentEnrollmentCount($student_id);
        if ($current_count + count($units_data) > 4) {
            throw new Exception("You can only enroll in a maximum of 4 courses. You currently have {$current_count} courses enrolled.");
        }
        
        // Get student's internal ID from student_id
        $student_sql = "SELECT id FROM students WHERE student_id = ?";
        $student_stmt = $conn->prepare($student_sql);
        $student_stmt->bind_param("s", $student_id);
        $student_stmt->execute();
        $student_result = $student_stmt->get_result();
        
        if ($student_result->num_rows === 0) {
            throw new Exception("Student not found");
        }
        
        $student_row = $student_result->fetch_assoc();
        $internal_student_id = $student_row['id'];
        
        // Get current semester
        $current_semester = date('Y') . (date('n') <= 6 ? '1' : '2');
        
        foreach ($units_data as $unit) {
            // Check if student is already enrolled in this unit
            $check_sql = "SELECT id FROM enrollments 
                         WHERE student_id = ? AND unit_id = ? AND semester = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("iis", $internal_student_id, $unit['unit_id'], $current_semester);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                throw new Exception("You are already enrolled in one or more selected units");
            }
            
            // Check prerequisites
            $prereq_sql = "SELECT p.prerequisite_unit_id, u.unit_code 
                          FROM unit_prerequisites p 
                          JOIN units u ON p.prerequisite_unit_id = u.id 
                          WHERE p.unit_id = ?";
            $prereq_stmt = $conn->prepare($prereq_sql);
            $prereq_stmt->bind_param("i", $unit['unit_id']);
            $prereq_stmt->execute();
            $prereq_result = $prereq_stmt->get_result();
            
            while ($prereq = $prereq_result->fetch_assoc()) {
                // Check if student has completed the prerequisite
                $completed_sql = "SELECT id FROM enrollments 
                                WHERE student_id = ? AND unit_id = ? AND status = 'approved'";
                $completed_stmt = $conn->prepare($completed_sql);
                $completed_stmt->bind_param("ii", $internal_student_id, $prereq['prerequisite_unit_id']);
                $completed_stmt->execute();
                $completed_result = $completed_stmt->get_result();
                
                if ($completed_result->num_rows == 0) {
                    throw new Exception("Prerequisite unit " . $prereq['unit_code'] . " not completed");
                }
            }
            
            // Insert enrollment
            $sql = "INSERT INTO enrollments (student_id, unit_id, semester, status) 
                    VALUES (?, ?, ?, 'pending')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $internal_student_id, $unit['unit_id'], $current_semester);
            $stmt->execute();
            
            // Create notification for admin
            $notification_sql = "INSERT INTO notifications (user_id, type, message, link) 
                               SELECT a.id, 'enrollment', 
                               CONCAT('New enrollment request for unit ', u.unit_code), 
                               CONCAT('admin/enrollments.php?student=', ?, '&unit=', ?)
                               FROM admins a, units u 
                               WHERE u.id = ?";
            $notification_stmt = $conn->prepare($notification_sql);
            $notification_stmt->bind_param("iii", $student_id, $unit['unit_id'], $unit['unit_id']);
            $notification_stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_SESSION["student_id"])) {
            throw new Exception('Not logged in');
        }
        
        $student_id = $_SESSION["student_id"];
        $units_data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($units_data)) {
            throw new Exception('No units selected');
        }
        
        registerUnits($student_id, $units_data);
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?> 