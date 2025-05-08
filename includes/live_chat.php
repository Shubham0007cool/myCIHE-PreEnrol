<?php
// Live chat container
?>
<div class="live-chat-container">
    <button class="chat-button" id="chatButton">
        <i class="fas fa-comments"></i> Live Chat
    </button>
    
    <div class="chat-window" id="chatWindow">
        <div class="chat-header">
            <h3>Live Chat Support</h3>
            <button class="close-chat" id="closeChat">✖</button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="chat-message bot-message">
                <p>Hello! How can we help you today?</p>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="userMessage" placeholder="Type your message here...">
            <button id="sendMessage"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<style>
    .live-chat-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    
    .chat-button {
        background-color: #e05d00;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 30px;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .chat-button:hover {
        background-color: rgb(0, 255, 200);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }
    
    .chat-window {
        width: 350px;
        height: 450px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        display: none;
        flex-direction: column;
        overflow: hidden;
    }
    
    .chat-header {
        background-color: #e05d00;
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-header h3 {
        margin: 0;
        font-size: 16px;
    }
    
    .close-chat {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }
    
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f9f9f9;
    }
    
    .chat-message {
        margin-bottom: 15px;
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 18px;
        line-height: 1.4;
    }
    
    .bot-message {
        background-color: #e0e0e0;
        border-top-left-radius: 5px;
        align-self: flex-start;
    }
    
    .user-message {
        background-color: #00a0e0;
        color: white;
        border-top-right-radius: 5px;
        margin-left: auto;
    }
    
    .chat-input {
        display: flex;
        padding: 10px;
        border-top: 1px solid #ddd;
        background-color: white;
    }
    
    .chat-input input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
    }
    
    .chat-input button {
        background-color: #e05d00;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        margin-left: 10px;
        cursor: pointer;
    }
    
    .chat-input button:hover {
        background-color: #c04f00;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatButton = document.getElementById('chatButton');
        const chatWindow = document.getElementById('chatWindow');
        const closeChat = document.getElementById('closeChat');
        const sendMessage = document.getElementById('sendMessage');
        const userMessage = document.getElementById('userMessage');
        const chatMessages = document.getElementById('chatMessages');
        
        chatButton.addEventListener('click', function() {
            chatWindow.style.display = 'flex';
            chatButton.style.display = 'none';
        });
        
        closeChat.addEventListener('click', function() {
            chatWindow.style.display = 'none';
            chatButton.style.display = 'flex';
        });
        
        function sendUserMessage() {
            const message = userMessage.value.trim();
            if (message) {
                const userMsgDiv = document.createElement('div');
                userMsgDiv.className = 'chat-message user-message';
                userMsgDiv.innerHTML = `<p>${message}</p>`;
                chatMessages.appendChild(userMsgDiv);
                
                userMessage.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                setTimeout(() => {
                    const botResponses = [
                        "I understand your concern. Let me check that for you.",
                        "Thanks for your message. Our support team will get back to you soon.",
                        "Could you provide more details about your question?",
                        "I'm fine, thanks for asking. How are you?",
                        "We're here to help! Is there anything specific you'd like to know?",
                        "I'll connect you with a support agent for further assistance.",
                        "Sorry for making you wait long time.."
                    ];
                    
                    const randomResponse = botResponses[Math.floor(Math.random() * botResponses.length)];
                    
                    const botMsgDiv = document.createElement('div');
                    botMsgDiv.className = 'chat-message bot-message';
                    botMsgDiv.innerHTML = `<p>${randomResponse}</p>`;
                    chatMessages.appendChild(botMsgDiv);
                    
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 1000);
            }
        }
        
        sendMessage.addEventListener('click', sendUserMessage);
        
        userMessage.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendUserMessage();
            }
        });
    });
</script> 