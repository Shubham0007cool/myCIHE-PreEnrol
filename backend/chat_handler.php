<?php
require_once 'db.php';

if (!isset($_SESSION)) {
    session_start();
}

class ChatHandler {
    private $conn;
    private $admin_id;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->admin_id = 'A' . ($_SESSION['admin_id'] ?? '');
    }

    public function getChatUsers() {
        if (!$this->admin_id) {
            return [];
        }

        // Get all students, including those who haven't chatted yet
        $sql = "SELECT DISTINCT 
                    s.student_id as id,
                    s.first_name,
                    s.last_name,
                    s.email,
                    (SELECT COUNT(*) FROM chat_messages 
                     WHERE sender_id = s.student_id 
                     AND receiver_id = ? 
                     AND is_read = 0) as unread_count,
                    (SELECT created_at FROM chat_messages 
                     WHERE (sender_id = s.student_id AND receiver_id = ?) 
                     OR (sender_id = ? AND receiver_id = s.student_id)
                     ORDER BY created_at DESC LIMIT 1) as last_message_time
                FROM students s
                LEFT JOIN chat_messages cm ON s.student_id = cm.sender_id OR s.student_id = cm.receiver_id
                ORDER BY CASE WHEN last_message_time IS NULL THEN 1 ELSE 0 END, 
                         last_message_time DESC, 
                         s.first_name ASC";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $this->admin_id, $this->admin_id, $this->admin_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = [
                'id' => $row['id'],
                'name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'unread_count' => $row['unread_count'],
                'last_message_time' => $row['last_message_time']
            ];
        }

        return $users;
    }

    public function getChatHistory($user_id) {
        // For student requests, we need to get admin ID
        if (isset($_SESSION['student_id'])) {
            $admin_sql = "SELECT id FROM admins LIMIT 1";
            $admin_result = mysqli_query($this->conn, $admin_sql);
            $admin = mysqli_fetch_assoc($admin_result);
            $this->admin_id = 'A' . $admin['id'];
        }

        if (!$this->admin_id) {
            return [];
        }

        // Get student info for the sender name
        $student_sql = "SELECT first_name, last_name FROM students WHERE student_id = ?";
        $student_stmt = mysqli_prepare($this->conn, $student_sql);
        mysqli_stmt_bind_param($student_stmt, "s", $user_id);
        mysqli_stmt_execute($student_stmt);
        $student_result = mysqli_stmt_get_result($student_stmt);
        $student = mysqli_fetch_assoc($student_result);
        $student_name = $student ? $student['first_name'] . ' ' . $student['last_name'] : 'Unknown Student';

        // Get admin info for the sender name
        $admin_sql = "SELECT email FROM admins WHERE id = ?";
        $admin_stmt = mysqli_prepare($this->conn, $admin_sql);
        $admin_id_num = substr($this->admin_id, 1); // Remove 'A' prefix
        mysqli_stmt_bind_param($admin_stmt, "s", $admin_id_num);
        mysqli_stmt_execute($admin_stmt);
        $admin_result = mysqli_stmt_get_result($admin_stmt);
        $admin = mysqli_fetch_assoc($admin_result);
        $admin_name = $admin ? 'Admin (' . $admin['email'] . ')' : 'Admin';

        $sql = "SELECT 
                    cm.*,
                    CASE 
                        WHEN cm.sender_id = ? THEN 'admin'
                        ELSE 'student'
                    END as message_type
                FROM chat_messages cm
                WHERE (cm.sender_id = ? AND cm.receiver_id = ?)
                   OR (cm.sender_id = ? AND cm.receiver_id = ?)
                ORDER BY cm.created_at ASC";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $this->admin_id, $this->admin_id, $user_id, $user_id, $this->admin_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // If sender is student, use their name, otherwise use admin name
            $sender_name = $row['sender_type'] === 'student' ? $student_name : $admin_name;
            
            $messages[] = [
                'id' => $row['id'],
                'message' => $row['message'],
                'sender_id' => $row['sender_id'],
                'receiver_id' => $row['receiver_id'],
                'sender_name' => $sender_name,
                'sender_type' => $row['sender_type'],
                'created_at' => $row['created_at'],
                'is_read' => $row['is_read'],
                'message_type' => $row['message_type']
            ];
        }

        return $messages;
    }

    public function getUnreadCount($user_id) {
        if (!$this->admin_id) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as count 
                FROM chat_messages 
                WHERE sender_id = ? 
                AND receiver_id = ? 
                AND is_read = 0";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $this->admin_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['count'];
    }

    public function markAsRead($user_id) {
        if (!$this->admin_id) {
            return false;
        }

        $sql = "UPDATE chat_messages 
                SET is_read = 1 
                WHERE sender_id = ? 
                AND receiver_id = ? 
                AND is_read = 0";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $this->admin_id);
        return mysqli_stmt_execute($stmt);
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat = new ChatHandler();
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_users':
            $users = $chat->getChatUsers();
            echo json_encode(['status' => 'success', 'users' => $users]);
            break;

        case 'get_history':
            $user_id = $_POST['user_id'] ?? '';
            $messages = $chat->getChatHistory($user_id);
            echo json_encode(['status' => 'success', 'messages' => $messages]);
            break;

        case 'mark_read':
            $user_id = $_POST['user_id'] ?? '';
            $success = $chat->markAsRead($user_id);
            echo json_encode(['status' => $success ? 'success' : 'error']);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} 