<?php
require_once 'db.php';

function getStudentProfile($student_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT 
                s.*,
                p.program_name,
                p.program_code
            FROM students s
            LEFT JOIN programs p ON s.program_id = p.id
            WHERE s.student_id = ?
        ");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    } catch (Exception $e) {
        error_log("Error fetching student profile: " . $e->getMessage());
        return null;
    }
}

function updateStudentProfile($student_id, $data) {
    global $conn;
    try {
        // Start transaction
        $conn->begin_transaction();

        // Update basic information
        $stmt = $conn->prepare("
            UPDATE students 
            SET 
                first_name = ?,
                last_name = ?,
                email = ?,
                contact_number = ?,
                emergency_contact = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE student_id = ?
        ");

        $stmt->bind_param(
            "ssssss",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['contact_number'],
            $data['emergency_contact'],
            $student_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update student profile");
        }

        // Commit transaction
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error updating student profile: " . $e->getMessage());
        return false;
    }
}

function getStudentEnrollments($student_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT 
                e.*,
                u.unit_code,
                u.unit_name,
                c.course_code,
                c.course_name,
                t.first_name as teacher_first_name,
                t.last_name as teacher_last_name
            FROM enrollments e
            JOIN units u ON e.unit_id = u.id
            JOIN courses c ON u.course_id = c.id
            LEFT JOIN teachers t ON u.teacher_id = t.id
            JOIN students s ON e.student_id = s.id
            WHERE s.student_id = ?
            ORDER BY e.enrollment_date DESC
        ");
        
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $enrollments = [];
        while ($row = $result->fetch_assoc()) {
            $enrollments[] = $row;
        }
        return $enrollments;
    } catch (Exception $e) {
        error_log("Error fetching student enrollments: " . $e->getMessage());
        return [];
    }
}

function getStudentNotifications($student_id) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT n.*
            FROM notifications n
            JOIN students s ON n.user_id = s.id
            WHERE s.student_id = ?
            ORDER BY n.created_at DESC
            LIMIT 10
        ");
        
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        return $notifications;
    } catch (Exception $e) {
        error_log("Error fetching student notifications: " . $e->getMessage());
        return [];
    }
}

// Handle profile update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();
    
    if (!isset($_SESSION['student_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $student_id = $_SESSION['student_id'];
    
    switch ($_POST['action']) {
        case 'update_profile':
            $data = [
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'emergency_contact' => $_POST['emergency_contact'] ?? ''
            ];
            
            if (updateStudentProfile($student_id, $data)) {
                $_SESSION['success'] = "Profile updated successfully!";
                echo json_encode(['success' => true]);
            } else {
                $_SESSION['error'] = "Failed to update profile. Please try again.";
                echo json_encode(['error' => 'Update failed']);
            }
            break;
            
        case 'get_profile':
            $profile = getStudentProfile($student_id);
            if ($profile) {
                echo json_encode($profile);
            } else {
                echo json_encode(['error' => 'Profile not found']);
            }
            break;
            
        case 'get_enrollments':
            $enrollments = getStudentEnrollments($student_id);
            echo json_encode($enrollments);
            break;
            
        case 'get_notifications':
            $notifications = getStudentNotifications($student_id);
            echo json_encode($notifications);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}
?> 