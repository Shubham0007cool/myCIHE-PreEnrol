<?php

if(!isset($_SESSION)) { 
    session_start();
}

require_once 'backend/db.php';
require_once 'backend/chat_handler.php';
require_once 'includes/admin_header.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get admin info
$admin_sql = "SELECT * FROM admins WHERE id = ?";
$admin_stmt = mysqli_prepare($conn, $admin_sql);
mysqli_stmt_bind_param($admin_stmt, "i", $_SESSION['admin_id']);
mysqli_stmt_execute($admin_stmt);
$admin_result = mysqli_stmt_get_result($admin_stmt);
$admin = mysqli_fetch_assoc($admin_result);

// Format admin ID
$admin_id = 'A' . $_SESSION['admin_id'];

// Initialize chat handler
$chat = new ChatHandler();
?>

<div class="admin-chat-container">
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-comments"></i> Live Chat Support</h3>
            <p class="admin-status">
                <span class="status-dot online"></span>
                Online
            </p>
        </div>
        <div class="search-box">
            <input type="text" id="searchUsers" placeholder="Search users...">
            <i class="fas fa-search"></i>
        </div>
        <div class="user-list" id="userList">
            <!-- User list will be populated here -->
        </div>
    </div>
    
    <div class="chat-main">
        <div class="chat-header">
            <div class="current-chat-info">
                <h3 id="currentUser">Select a user to start chatting</h3>
                <p class="user-status">Offline</p>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="no-chat-selected">
                <i class="fas fa-comments"></i>
                <p>Select a conversation to start chatting</p>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Type your message..." disabled>
            <button id="sendButton" disabled>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
.admin-chat-container {
    display: flex;
    height: calc(100vh - 60px);
    background-color: #f8f9fa;
    margin: 20px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.chat-sidebar {
    width: 320px;
    background-color: white;
    border-right: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    background-color: #e05d00;
    color: white;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.admin-status {
    margin: 5px 0 0;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #ccc;
}

.status-dot.online {
    background-color: #28a745;
}

.search-box {
    padding: 15px;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 10px 35px 10px 15px;
    border: 1px solid #e9ecef;
    border-radius: 20px;
    outline: none;
}

.search-box i {
    position: absolute;
    right: 25px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.user-list {
    flex: 1;
    overflow-y: auto;
}

.user-item {
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: background-color 0.2s;
}

.user-item:hover {
    background-color: #f8f9fa;
}

.user-item.active {
    background-color: #e8f4ff;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background-color: #e05d00;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 3px;
}

.last-message {
    display: block;
    font-size: 0.85em;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.unread-badge {
    background-color: #e05d00;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.8em;
    min-width: 20px;
    text-align: center;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background-color: white;
}

.chat-header {
    padding: 20px;
    background-color: white;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.current-chat-info h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 18px;
}

.user-status {
    margin: 5px 0 0;
    font-size: 14px;
    color: #6c757d;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
}

.no-chat-selected {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #6c757d;
}

.no-chat-selected i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #e05d00;
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
    background-color: #e05d00;
    color: white;
    border-top-right-radius: 5px;
}

.message.received .message-content {
    background-color: white;
    color: #2c3e50;
    border-top-left-radius: 5px;
}

.sender-name {
    font-size: 0.8em;
    color: #666;
    margin-bottom: 4px;
    font-weight: 500;
}

.message-text {
    word-wrap: break-word;
}

.message-time {
    font-size: 0.7em;
    color: #999;
    margin-top: 4px;
    text-align: right;
}

.chat-input {
    padding: 20px;
    background-color: white;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 10px;
}

.chat-input input {
    flex: 1;
    padding: 12px 20px;
    border: 1px solid #e9ecef;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
}

.chat-input input:focus {
    border-color: #e05d00;
}

.chat-input button {
    background-color: #e05d00;
    color: white;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    cursor: pointer;
    transition: all 0.2s;
}

.chat-input button:hover {
    background-color: #c04f00;
    transform: scale(1.05);
}

.chat-input button:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
    transform: none;
}
</style>

<script>
let ws;
let currentUserId = null;

// Connect to WebSocket server
function connectWebSocket() {
    ws = new WebSocket('ws://localhost:8081');
    
    ws.onopen = function() {
        console.log('Connected to chat server');
        // Register admin
        ws.send(JSON.stringify({
            type: 'register',
            user_id: '<?php echo htmlspecialchars($admin_id); ?>',
            name: '<?php echo htmlspecialchars($admin['email']); ?>',
            user_type: 'admin',
            email: '<?php echo htmlspecialchars($admin['email']); ?>'
        }));
    };
    
    ws.onmessage = function(e) {
        const data = JSON.parse(e.data);
        console.log('Admin received message:', data);
        
        switch(data.type) {
            case 'message':
                // If this is a message from the currently selected user or to the current user
                if (data.sender_id === currentUserId || data.receiver_id === currentUserId) {
                    appendMessage(data);
                    markAsRead(currentUserId);
                }
                // Update the user list to show new message
                loadUsers();
                break;
                
            case 'message_status':
                console.log('Message status:', data.status, 'Delivered:', data.delivered);
                break;
                
            case 'system':
                console.log('System message:', data);
                break;
                
            case 'error':
                console.error('Chat error:', data.message);
                break;
        }
    };
    
    ws.onclose = function() {
        console.log('Disconnected from chat server');
        setTimeout(connectWebSocket, 1000);
    };
}

// Load chat users
function loadUsers() {
    fetch('backend/chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_users'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const userList = document.getElementById('userList');
            userList.innerHTML = '';
            
            data.users.forEach(user => {
                const userItem = document.createElement('div');
                userItem.className = `user-item ${user.id === currentUserId ? 'active' : ''}`;
                userItem.innerHTML = `
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name">${user.name}</span>
                        <span class="last-message">Click to view conversation</span>
                    </div>
                    ${user.unread_count > 0 ? 
                        `<span class="unread-badge">${user.unread_count}</span>` : 
                        ''}
                `;
                
                userItem.onclick = () => selectUser(user.id, user.name);
                userList.appendChild(userItem);
            });
        }
    });
}

// Select user to chat with
function selectUser(userId, userName) {
    currentUserId = userId;
    document.getElementById('currentUser').textContent = `Chat with ${userName}`;
    document.getElementById('messageInput').disabled = false;
    document.getElementById('sendButton').disabled = false;
    
    // Clear existing messages
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '';
    
    // Load chat history
    loadChatHistory(userId);
    
    // Mark messages as read
    markAsRead(userId);
    
    // Update user list to remove unread badge
    loadUsers();
}

// Load chat history
function loadChatHistory(userId) {
    fetch('backend/chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_history&user_id=${userId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.innerHTML = '';
            
            data.messages.forEach(message => {
                appendMessage(message);
            });
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
}

// Append message to chat
function appendMessage(data) {
    const chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) {
        console.error('Chat messages container not found');
        return;
    }

    // Remove the "no chat selected" message if it exists
    const noChatSelected = chatMessages.querySelector('.no-chat-selected');
    if (noChatSelected) {
        noChatSelected.remove();
    }

    const messageDiv = document.createElement('div');
    const isSent = data.sender_id === '<?php echo htmlspecialchars($admin_id); ?>';
    
    messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
    
    const messageContent = document.createElement('div');
    messageContent.className = 'message-content';
    
    // Add sender name for received messages
    if (!isSent) {
        const senderName = document.createElement('div');
        senderName.className = 'sender-name';
        senderName.textContent = data.sender_name;
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
    timestamp.textContent = new Date(data.created_at || new Date()).toLocaleString();
    messageContent.appendChild(timestamp);
    
    messageDiv.appendChild(messageContent);
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Mark messages as read
function markAsRead(userId) {
    fetch('backend/chat_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=mark_read&user_id=${userId}`
    });
}

// Send message
document.getElementById('sendButton').onclick = function() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (message && currentUserId) {
        const messageData = {
            type: 'message',
            sender_id: '<?php echo htmlspecialchars($admin_id); ?>',
            receiver_id: currentUserId,
            message: message,
            sender_name: '<?php echo htmlspecialchars($admin['email']); ?>',
            sender_type: 'admin'
        };
        
        ws.send(JSON.stringify(messageData));
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

// Search users
document.getElementById('searchUsers').onkeyup = function() {
    const searchText = this.value.toLowerCase();
    const userItems = document.querySelectorAll('.user-item');
    
    userItems.forEach(item => {
        const userName = item.querySelector('.user-name').textContent.toLowerCase();
        if (userName.includes(searchText)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
};

// Initialize
connectWebSocket();
loadUsers();
setInterval(loadUsers, 30000); // Refresh user list every 30 seconds
</script>