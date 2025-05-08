<?php

if (!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in as student
if (!isset($_SESSION['student_id'])) {
    return; // Don't show chat if not logged in as student
}

// Get student info from database
require_once 'backend/db.php';
$student_id = $_SESSION['student_id'];
$student_sql = "SELECT first_name, last_name, email FROM students WHERE student_id = ?";
$student_stmt = mysqli_prepare($conn, $student_sql);
mysqli_stmt_bind_param($student_stmt, "s", $student_id);
mysqli_stmt_execute($student_stmt);
$student_result = mysqli_stmt_get_result($student_stmt);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    return; // Don't show chat if student not found
}

// Get admin ID
$admin_sql = "SELECT id FROM admins LIMIT 1";
$admin_result = mysqli_query($conn, $admin_sql);
$admin = mysqli_fetch_assoc($admin_result);
$admin_id = 'A' . $admin['id']; // Format admin ID as string with 'A' prefix

$student_name = $student['first_name'] . ' ' . $student['last_name'];
$student_email = $student['email'];
?>

<div class="chat-widget" id="chatWidget">
    <div class="chat-header" onclick="toggleChat()">
        <i class="fas fa-comments"></i>
        <span>Live Chat Support</span>
        <div class="chat-status">
            <span class="status-dot"></span>
            <span class="status-text">Connecting...</span>
        </div>
    </div>
    
    <div class="chat-body" id="chatBody">
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be displayed here -->
        </div>
        
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Type your message...">
            <button id="sendButton">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
.chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
    width: 350px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    overflow: hidden;
        transition: all 0.3s ease;
    }
    
.chat-widget.minimized .chat-body {
        display: none;
    }
    
    .chat-header {
    background: #e05d00;
        color: white;
        padding: 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chat-header i {
    font-size: 20px;
}

.chat-status {
    margin-left: auto;
        display: flex;
        align-items: center;
    gap: 5px;
    font-size: 14px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ccc;
}

.status-dot.connected {
    background: #28a745;
}

.chat-body {
    height: 400px;
    display: flex;
    flex-direction: column;
    }
    
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
    }
    
    .message {
        margin: 10px 0;
        display: flex;
        flex-direction: column;
        max-width: 80%;
    }
    
    .message.sent {
        align-self: flex-end;
    }
    
    .message.received {
        align-self: flex-start;
    }
    
    .message-content {
        padding: 12px 16px;
        border-radius: 15px;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .message.sent .message-content {
        background: #e05d00;
        color: white;
        border-top-right-radius: 5px;
    }
    
    .message.received .message-content {
        background: white;
        color: #2c3e50;
        border-top-left-radius: 5px;
    }
    
    .sender-name {
        font-size: 0.8em;
        color: #666;
        margin-bottom: 4px;
        font-weight: 500;
    }
    
    .message-time {
        font-size: 0.7em;
        color: #999;
        margin-top: 4px;
        text-align: right;
    }
    
    .chat-input {
    padding: 15px;
    background: white;
    border-top: 1px solid #e9ecef;
        display: flex;
    gap: 10px;
    }
    
    .chat-input input {
        flex: 1;
    padding: 10px 15px;
    border: 1px solid #e9ecef;
        border-radius: 20px;
        outline: none;
    }

.chat-input input:focus {
    border-color: #e05d00;
}
    
    .chat-input button {
    background: #e05d00;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
    transition: all 0.2s;
    }
    
    .chat-input button:hover {
    background: #c04f00;
    transform: scale(1.05);
}

.chat-input button:disabled {
    background: #e9ecef;
    cursor: not-allowed;
    transform: none;
}

.message.error .message-content {
    background: #dc3545;
    color: white;
    padding: 10px 15px;
    border-radius: 15px;
    margin: 10px 0;
}

.message.error i {
    margin-right: 5px;
    }
</style>

<script>
let ws;
let isConnected = false;
let messageQueue = [];
let reconnectAttempts = 0;
const MAX_RECONNECT_ATTEMPTS = 5;
const RECONNECT_DELAY = 3000; // 3 seconds

// Load chat history when connected
function loadChatHistory() {
    console.log('Loading chat history...');
    fetch('backend/chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_history&user_id=<?php echo htmlspecialchars($student_id); ?>`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Chat history response:', data);
        if (data.status === 'success' && Array.isArray(data.messages)) {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            
            data.messages.forEach(message => {
                console.log('Processing message:', message);
                const messageData = {
                    message: message.message,
                    sender_id: message.sender_id,
                    receiver_id: message.receiver_id,
                    sender_name: message.sender_name,
                    sender_type: message.sender_type,
                    created_at: message.created_at,
                    is_read: message.is_read
                };
                appendMessage(messageData);
            });
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } else {
            console.error('Invalid chat history response:', data);
            showError('Failed to load chat history. Please refresh the page.');
        }
    })
    .catch(error => {
        console.error('Error loading chat history:', error);
        showError('Failed to load chat history. Please refresh the page.');
    });
}

// Connect to WebSocket server
function connectWebSocket() {
    try {
        ws = new WebSocket('ws://localhost:8081');
        
        ws.onopen = function() {
            console.log('Connected to chat server');
            isConnected = true;
            reconnectAttempts = 0;
            updateStatus(true);
            
            // Register student
            const registerData = {
                type: 'register',
                user_id: '<?php echo htmlspecialchars($student_id); ?>',
                name: '<?php echo htmlspecialchars($student_name); ?>',
                user_type: 'student',
                email: '<?php echo htmlspecialchars($student_email); ?>'
            };
            
            try {
                ws.send(JSON.stringify(registerData));
                console.log('Registration data sent:', registerData);
                // Load chat history after successful registration
                loadChatHistory();
            } catch (error) {
                console.error('Error sending registration data:', error);
            }
        };
        
        ws.onmessage = function(e) {
            try {
                const data = JSON.parse(e.data);
                console.log('Student received message:', data);
                
                switch(data.type) {
                    case 'system':
                        if (data.status === 'connected') {
                            console.log('Registration successful');
                            // Send queued messages after successful registration
                            while (messageQueue.length > 0) {
                                const message = messageQueue.shift();
                                try {
                                    ws.send(JSON.stringify(message));
                                } catch (error) {
                                    console.error('Error sending queued message:', error);
                                    messageQueue.unshift(message);
                                    break;
                                }
                            }
                            // Load chat history after successful registration
                            loadChatHistory();
                        }
                        break;
                        
                    case 'message':
                        console.log('Processing message:', {
                            sender_id: data.sender_id,
                            receiver_id: data.receiver_id,
                            student_id: '<?php echo htmlspecialchars($student_id); ?>'
                        });
                        appendMessage(data);
                        break;
                        
                    case 'message_status':
                        console.log('Message status:', data.status, 'Delivered:', data.delivered);
                        break;
                        
                    case 'error':
                        console.error('Chat error:', data.message);
                        showError(data.message);
                        break;
                }
            } catch (error) {
                console.error('Error processing message:', error);
            }
        };
        
        ws.onclose = function(e) {
            console.log('WebSocket connection closed:', e.code, e.reason);
            isConnected = false;
            updateStatus(false);
            
            if (reconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                reconnectAttempts++;
                console.log(`Attempting to reconnect (${reconnectAttempts}/${MAX_RECONNECT_ATTEMPTS})...`);
                setTimeout(connectWebSocket, RECONNECT_DELAY);
            } else {
                console.error('Max reconnection attempts reached');
                showError('Unable to connect to chat server. Please refresh the page to try again.');
            }
        };
        
        ws.onerror = function(error) {
            console.error('WebSocket error:', error);
            showError('Connection error. Please check if the chat server is running.');
        };
    } catch (error) {
        console.error('Error creating WebSocket connection:', error);
        showError('Failed to connect to chat server. Please refresh the page to try again.');
    }
}

// Show error message in chat
function showError(message) {
    const chatMessages = document.getElementById('chatMessages');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'message error';
    errorDiv.innerHTML = `
        <div class="message-content">
            <i class="fas fa-exclamation-circle"></i>
            ${message}
        </div>
    `;
    chatMessages.appendChild(errorDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Update connection status
function updateStatus(connected) {
    const statusDot = document.querySelector('.status-dot');
    const statusText = document.querySelector('.status-text');
    
    if (connected) {
        statusDot.classList.add('connected');
        statusText.textContent = 'Connected';
    } else {
        statusDot.classList.remove('connected');
        statusText.textContent = reconnectAttempts < MAX_RECONNECT_ATTEMPTS ? 'Reconnecting...' : 'Disconnected';
    }
}

// Toggle chat window
function toggleChat() {
    const chatWidget = document.getElementById('chatWidget');
    chatWidget.classList.toggle('minimized');
}

// Append message to chat
function appendMessage(data) {
    console.log('Appending message:', data);
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) {
        console.error('Chat messages container not found');
        return;
    }

    const messageDiv = document.createElement('div');
    const isSent = data.sender_id === '<?php echo htmlspecialchars($student_id); ?>';
    console.log('Message is sent:', isSent, 'Sender ID:', data.sender_id, 'Student ID:', '<?php echo htmlspecialchars($student_id); ?>');
    
    messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
    
    const messageContent = document.createElement('div');
    messageContent.className = 'message-content';
    
    // Add sender name for received messages
    if (!isSent) {
        const senderName = document.createElement('div');
        senderName.className = 'sender-name';
        senderName.textContent = data.sender_name || 'Admin';
        messageContent.appendChild(senderName);
    }
    
    // Add message text
    const messageText = document.createElement('div');
    messageText.className = 'message-text';
    messageText.textContent = data.message;
    messageContent.appendChild(messageText);
    
    // Add timestamp
    const timestamp = document.createElement('div');
    timestamp.className = 'message-time';
    const messageDate = data.created_at ? new Date(data.created_at) : new Date();
    timestamp.textContent = messageDate.toLocaleString();
    messageContent.appendChild(timestamp);
    
    messageDiv.appendChild(messageContent);
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    console.log('Message appended to chat');
}

// Send message
document.getElementById('sendButton').onclick = function() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (message) {
        const messageData = {
            type: 'message',
            sender_id: '<?php echo htmlspecialchars($student_id); ?>',
            receiver_id: '<?php echo htmlspecialchars($admin_id); ?>',
            message: message,
            sender_name: '<?php echo htmlspecialchars($student_name); ?>',
            sender_type: 'student'
        };
        
        if (isConnected) {
            ws.send(JSON.stringify(messageData));
        } else {
            messageQueue.push(messageData);
        }

        appendMessage(messageData);
        
        messageInput.value = '';
    }
};

// Handle Enter key
document.getElementById('messageInput').onkeypress = function(e) {
            if (e.key === 'Enter') {
        document.getElementById('sendButton').click();
    }
};

// Initialize
connectWebSocket();

</script> 