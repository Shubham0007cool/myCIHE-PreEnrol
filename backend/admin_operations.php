<?php
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
   session_start();
}

function getDashboardStats() {
    global $conn;
    
    $stats = [
        'total_enrollments' => 0,
        'pending_approvals' => 0,
        'popular_course' => '',
        'popular_course_count' => 0,
        'enrollment_period' => '',
        'closing_date' => ''
    ];
    
    // Get total enrollments
    $sql = "SELECT COUNT(*) as total FROM enrollments";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['total_enrollments'] = $row['total'];
    }
    
    // Get pending approvals
    $sql = "SELECT COUNT(*) as pending FROM enrollments WHERE status = 'pending'";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['pending_approvals'] = $row['pending'];
    }
    
    // Get most popular course
    $sql = "SELECT u.unit_code, u.unit_name, COUNT(e.id) as enrollment_count 
            FROM enrollments e 
            JOIN units u ON e.unit_id = u.id 
            GROUP BY u.id 
            ORDER BY enrollment_count DESC 
            LIMIT 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $stats['popular_course'] = $row['unit_code'] . ' - ' . $row['unit_name'];
        $stats['popular_course_count'] = $row['enrollment_count'];
    }
    
    // Set enrollment period
    $current_month = date('n');
    $current_year = date('Y');
    $stats['enrollment_period'] = 'Semester ' . ($current_month <= 6 ? '1' : '2') . ', ' . $current_year;
    $stats['closing_date'] = ($current_month <= 6 ? 'June 30' : 'December 31') . ', ' . $current_year;
    
    return $stats;
}

function getRecentEnrollments($limit = 4) {
    global $conn;
    
    $sql = "SELECT s.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
                   u.unit_code, u.unit_name, c.course_name as faculty, e.enrollment_date
            FROM enrollments e
            JOIN students s ON e.student_id = s.id
            JOIN units u ON e.unit_id = u.id
            JOIN courses c ON u.course_id = c.id
            ORDER BY e.enrollment_date DESC
            LIMIT ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $enrollments = [];
    while ($row = $result->fetch_assoc()) {
        $enrollments[] = [
            'student_name' => $row['student_name'],
            'student_id' => $row['student_id'],
            'faculty' => $row['faculty'],
            'courses' => $row['unit_code'] . ', ' . $row['unit_name'],
            'enrollment_time' => date('F j, Y g:i A', strtotime($row['enrollment_date']))
        ];
    }
    
    return $enrollments;
}

function changeAdminPassword($adminId, $currentPassword, $newPassword) {
    global $conn;
    
    try {
        // First verify current password
        $sql = "SELECT password FROM admins WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Admin not found");
        }
        
        $admin = $result->fetch_assoc();
        
        // Verify current password
        if (!password_verify($currentPassword, $admin['password'])) {
            throw new Exception("Current password is incorrect");
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $sql = "UPDATE admins SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $hashedPassword, $adminId);
        $stmt->execute();
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error changing password: " . $e->getMessage());
        throw $e;
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_stats':
            echo json_encode(getDashboardStats());
            break;
            
        case 'get_recent_enrollments':
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 4;
            echo json_encode(getRecentEnrollments($limit));
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'change_password':
                    if (!isset($_SESSION['admin_id'])) {
                        throw new Exception("Not logged in");
                    }
                    
                    $currentPassword = $_POST['current_password'] ?? '';
                    $newPassword = $_POST['new_password'] ?? '';
                    
                    if (empty($currentPassword) || empty($newPassword)) {
                        throw new Exception("Missing required parameters");
                    }
                    
                    changeAdminPassword($_SESSION['admin_id'], $currentPassword, $newPassword);
                    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
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