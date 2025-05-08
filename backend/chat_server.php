<?php
require 'vendor/autoload.php';
require_once 'db.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $users = [];
    protected $conn;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        
        // Get database credentials from db.php
        global $conn;
        $this->conn = $conn;
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        echo "Chat server started\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        try {
            $data = json_decode($msg);
            if (!$data) {
                throw new Exception("Invalid JSON message");
            }
            
            echo "Received message: " . $msg . "\n";

            if ($data->type === 'register') {
                // Store user information
                $this->users[$from->resourceId] = [
                    'user_id' => $data->user_id,
                    'name' => $data->name,
                    'user_type' => $data->user_type,
                    'email' => $data->email
                ];
                
                echo "User registered: {$data->user_id} ({$data->email})\n";
                
                // Send confirmation back to the user
                $response = [
                    'type' => 'system',
                    'message' => 'Registration successful',
                    'status' => 'connected',
                    'user_id' => $data->user_id
                ];
                
                $from->send(json_encode($response));
                echo "Sent registration confirmation to {$data->user_id}\n";
                return;
            }

            if ($data->type === 'message') {
                // Get sender information
                $sender = $this->users[$from->resourceId] ?? null;
                if (!$sender) {
                    $from->send(json_encode([
                        'type' => 'error',
                        'message' => 'You must register first'
                    ]));
                    return;
                }

                // Store message in database
                $message_id = $this->storeMessage(
                    $data->sender_id,
                    $data->receiver_id,
                    $data->message,
                    $sender['user_type']
                );

                if (!$message_id) {
                    $from->send(json_encode([
                        'type' => 'error',
                        'message' => 'Failed to store message'
                    ]));
                    return;
                }

                // Prepare message data
                $messageData = [
                    'type' => 'message',
                    'message_id' => $message_id,
                    'sender_id' => $data->sender_id,
                    'receiver_id' => $data->receiver_id,
                    'message' => $data->message,
                    'sender_name' => $sender['name'],
                    'sender_type' => $sender['user_type'],
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Send to receiver if online
                $receiverFound = false;
                foreach ($this->clients as $client) {
                    if (isset($this->users[$client->resourceId]) && 
                        $this->users[$client->resourceId]['user_id'] === $data->receiver_id) {
                        $client->send(json_encode($messageData));
                        $receiverFound = true;
                        break;
                    }
                }

                // Send confirmation to sender
                $from->send(json_encode([
                    'type' => 'message_status',
                    'message_id' => $message_id,
                    'status' => 'sent',
                    'delivered' => $receiverFound
                ]));

                // If receiver is not online, store as unread
                if (!$receiverFound) {
                    $this->markMessageAsUnread($message_id);
                }
            }
        } catch (Exception $e) {
            echo "Error processing message: " . $e->getMessage() . "\n";
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Error processing message: ' . $e->getMessage()
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        if (isset($this->users[$conn->resourceId])) {
            $user = $this->users[$conn->resourceId];
            echo "User disconnected: {$user['user_id']} ({$user['email']})\n";
            unset($this->users[$conn->resourceId]);
        }
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        if (isset($this->users[$conn->resourceId])) {
            $user = $this->users[$conn->resourceId];
            echo "Error for user: {$user['user_id']} ({$user['email']})\n";
        }
        $conn->close();
    }

    protected function storeMessage($sender_id, $receiver_id, $message, $sender_type) {
        try {
            // Store the message
            $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, sender_type, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                echo "Prepare failed: " . $this->conn->error . "\n";
                return false;
            }

            $stmt->bind_param("ssss", $sender_id, $receiver_id, $message, $sender_type);
            
            if (!$stmt->execute()) {
                echo "Execute failed: " . $stmt->error . "\n";
                return false;
            }

            $message_id = $this->conn->insert_id;
            $stmt->close();
            return $message_id;
        } catch (Exception $e) {
            echo "Error storing message: " . $e->getMessage() . "\n";
            return false;
        }
    }

    protected function markMessageAsUnread($message_id) {
        try {
            $sql = "UPDATE chat_messages SET is_read = 0 WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $message_id);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            echo "Error marking message as unread: " . $e->getMessage() . "\n";
        }
    }
}

// Create WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8081
);

echo "Chat server started on port 8081\n";
$server->run(); 