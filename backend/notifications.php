<?php
require_once "db.php";
require '../vendor/autoload.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;

session_start();

class NotificationSystem {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createNotification($user_id, $type, $message, $link = null) {
        $sql = "INSERT INTO notifications (user_id, type, message, link) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "isss", $user_id, $type, $message, $link);
            return mysqli_stmt_execute($stmt);
        }
        return false;
    }
    
    public function getNotifications($user_id, $limit = 10) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $limit);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $notifications = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $notifications[] = [
                    'id' => $row['id'],
                    'type' => $row['type'],
                    'message' => $row['message'],
                    'link' => $row['link'],
                    'created_at' => $row['created_at'],
                    'read' => $row['read']
                ];
            }
            return $notifications;
        }
        return [];
    }
    
    public function markAsRead($notification_id) {
        $sql = "UPDATE notifications SET `read` = 1 WHERE id = ?";
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $notification_id);
            return mysqli_stmt_execute($stmt);
        }
        return false;
    }
    
    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND `read` = 0";
        if ($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            return $row['count'];
        }
        return 0;
    }
}

// Handle WebSocket connections for real-time notifications
if (isset($_GET['ws'])) {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new class implements \Ratchet\MessageComponentInterface {
                    protected $clients;

                    public function __construct() {
                        $this->clients = new \SplObjectStorage;
                    }

                    public function onOpen(\Ratchet\ConnectionInterface $conn) {
                        $this->clients->attach($conn);
                    }

                    public function onMessage(\Ratchet\ConnectionInterface $from, $msg) {
                        $data = json_decode($msg, true);
                        if ($data['type'] === 'subscribe') {
                            $from->user_id = $data['user_id'];
                        }
                    }

                    public function onClose(\Ratchet\ConnectionInterface $conn) {
                        $this->clients->detach($conn);
                    }

                    public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
                        $conn->close();
                    }
                }
            )
        ),
        8080
    );
    
    $server->run();
    exit;
}

// Handle HTTP requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notification = new NotificationSystem($conn);
    $data = json_decode(file_get_contents('php://input'), true);
    
    switch ($data['action']) {
        case 'get_notifications':
            $result = $notification->getNotifications($_SESSION['id']);
            break;
            
        case 'mark_read':
            $result = $notification->markAsRead($data['notification_id']);
            break;
            
        case 'get_unread_count':
            $result = $notification->getUnreadCount($_SESSION['id']);
            break;
            
        default:
            $result = false;
    }
    
    echo json_encode([
        "status" => "success",
        "data" => $result
    ]);
}

mysqli_close($conn);
?> 