<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$character = isset($_GET['character']) ? htmlspecialchars($_GET['character']) : 'AI Assistant';

// Database connection
$host = 'localhost';
$dbname = 'spicychat';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'send_message') {
        $message = trim($_POST['message']);
        $chat_id = $_POST['chat_id'] ?? null;
        
        if (!$chat_id) {
            // Create new chat
            $stmt = $pdo->prepare("INSERT INTO chats (user_id, character_name, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $character]);
            $chat_id = $pdo->lastInsertId();
        }
        
        // Save user message
        $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender, message, created_at) VALUES (?, 'user', ?, NOW())");
        $stmt->execute([$chat_id, $message]);
        
        // Generate AI response (simple example - you would integrate with actual AI API)
        $ai_responses = [
            "That's interesting! Tell me more.",
            "I understand what you mean. How does that make you feel?",
            "Fascinating! I'd love to hear more about your thoughts on this.",
            "That's a great perspective. What led you to that conclusion?",
            "I see where you're coming from. Let's explore this further.",
        ];
        $ai_response = $ai_responses[array_rand($ai_responses)];
        
        // Save AI message
        $stmt = $pdo->prepare("INSERT INTO messages (chat_id, sender, message, created_at) VALUES (?, 'ai', ?, NOW())");
        $stmt->execute([$chat_id, $ai_response]);
        
        echo json_encode([
            'success' => true,
            'chat_id' => $chat_id,
            'ai_message' => $ai_response
        ]);
        exit;
    }
    
    if ($_POST['action'] == 'load_messages') {
        $chat_id = $_POST['chat_id'];
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
        $stmt->execute([$chat_id]);
        $messages = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo $character; ?> - SpicyChat.AI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0a0a;
            color: #fff;
            height: 100vh;
            overflow: hidden;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .chat-header {
            background: #111;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #222;
        }

        .character-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .character-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .character-name {
            font-size: 18px;
            font-weight: bold;
        }

        .character-tags {
            display: flex;
            gap: 8px;
            font-size: 12px;
            color: #999;
        }

        .tag {
            padding: 3px 8px;
            background: #2a2a2a;
            border-radius: 4px;
        }

        .menu-btn {
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            font-size: 24px;
            padding: 5px 10px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .menu-btn:hover {
            background: #2a2a2a;
        }

        .menu-dropdown {
            position: absolute;
            top: 60px;
            right: 30px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 10px;
            display: none;
            min-width: 250px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .menu-dropdown.show {
            display: block;
        }

        .menu-item {
            padding: 12px 15px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-item:hover {
            background: #2a2a2a;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            display: flex;
            gap: 15px;
            max-width: 80%;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .message.user .message-avatar {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .message-content {
            background: #1a1a1a;
            padding: 15px 18px;
            border-radius: 18px;
            line-height: 1.6;
        }

        .message.user .message-content {
            background: #2a2a4a;
        }

        .message-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .chat-input-container {
            padding: 20px 30px;
            background: #111;
            border-top: 1px solid #222;
        }

        .chat-disclaimer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }

        .chat-input-wrapper {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            max-width: 1200px;
            margin: 0 auto;
        }

        .chat-input {
            flex: 1;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 15px 20px;
            color: #fff;
            font-size: 15px;
            resize: none;
            max-height: 150px;
            font-family: inherit;
        }

        .chat-input:focus {
            outline: none;
            border-color: #8b5cf6;
        }

        .input-actions {
            display: flex;
            gap: 8px;
        }

        .input-btn {
            width: 45px;
            height: 45px;
            background: #2a2a2a;
            border: none;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            font-size: 20px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-btn:hover {
            background: #333;
        }

        .send-btn {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        }

        .send-btn:hover {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
        }

        .typing-indicator {
            display: none;
            align-items: center;
            gap: 5px;
            padding: 15px 18px;
            background: #1a1a1a;
            border-radius: 18px;
            width: fit-content;
        }

        .typing-indicator.show {
            display: flex;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: #666;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.7;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="character-info">
                <div class="character-avatar">🎭</div>
                <div>
                    <div class="character-name"><?php echo $character; ?></div>
                    <div class="character-tags">
                        <span class="tag">Male</span>
                        <span class="tag">Romantic</span>
                        <span class="tag">Mystery</span>
                    </div>
                </div>
            </div>
            <button class="menu-btn" onclick="toggleMenu()">⋮</button>
        </div>

        <div class="menu-dropdown" id="menuDropdown">
            <div class="menu-item" onclick="startNewChat()">💬 Start New Chat</div>
            <div class="menu-item">📋 View Saved Chats</div>
            <div class="menu-item">🗑️ Remove Messages</div>
            <div class="menu-item">🎭 Change Persona</div>
            <div class="menu-item">⭐ Add Bots to Favorite</div>
            <div class="menu-item">🚫 Block Creator</div>
            <div class="menu-item">⚙️ Generation Settings</div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="message">
                <div class="message-avatar">🎭</div>
                <div>
                    <div class="message-content">
                        Hello! I'm <?php echo $character; ?>. It's wonderful to meet you! How can I assist you today?
                    </div>
                    <div class="message-info">
                        <span>🔊</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-input-container">
            <div class="chat-disclaimer">
                Spicychat is powered by AI for creative storytelling and roleplay. All conversations are fictional. Enjoy responsibly!
            </div>
            <div class="chat-input-wrapper">
                <button class="input-btn" title="Attach Image">📎</button>
                <textarea 
                    class="chat-input" 
                    id="messageInput" 
                    placeholder="Message..." 
                    rows="1"
                    onkeydown="handleKeyPress(event)"
                ></textarea>
                <button class="input-btn" title="Voice Input">🎤</button>
                <button class="input-btn send-btn" onclick="sendMessage()" title="Send">➤</button>
            </div>
        </div>
    </div>

    <script>
        let chatId = null;

        function toggleMenu() {
            const menu = document.getElementById('menuDropdown');
            menu.classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('menuDropdown');
            const btn = document.querySelector('.menu-btn');
            if (!menu.contains(e.target) && e.target !== btn) {
                menu.classList.remove('show');
            }
        });

        function startNewChat() {
            if (confirm('Start a new chat? Current conversation will be saved.')) {
                chatId = null;
                document.getElementById('chatMessages').innerHTML = `
                    <div class="message">
                        <div class="message-avatar">🎭</div>
                        <div>
                            <div class="message-content">
                                Hello! I'm <?php echo $character; ?>. It's wonderful to meet you! How can I assist you today?
                            </div>
                            <div class="message-info">
                                <span>🔊</span>
                            </div>
                        </div>
                    </div>
                `;
            }
            toggleMenu();
        }

        function addMessage(sender, text) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const avatar = sender === 'user' ? '👤' : '🎭';
            
            messageDiv.innerHTML = `
                <div class="message-avatar">${avatar}</div>
                <div>
                    <div class="message-content">${text}</div>
                    <div class="message-info">
                        <span>${sender === 'ai' ? '🔊' : ''}</span>
                    </div>
                </div>
            `;
            
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function showTyping() {
            const messagesDiv = document.getElementById('chatMessages');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `
                <div class="message-avatar">🎭</div>
                <div class="typing-indicator show">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            `;
            messagesDiv.appendChild(typingDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function hideTyping() {
            const typing = document.getElementById('typingIndicator');
            if (typing) typing.remove();
        }

        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            addMessage('user', message);
            input.value = '';
            input.style.height = 'auto';
            
            showTyping();
            
            try {
                const formData = new FormData();
                formData.append('action', 'send_message');
                formData.append('message', message);
                if (chatId) formData.append('chat_id', chatId);
                
                const response = await fetch('chat.php?character=<?php echo urlencode($character); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                hideTyping();
                
                if (data.success) {
                    chatId = data.chat_id;
                    addMessage('ai', data.ai_message);
                }
            } catch (error) {
                hideTyping();
                addMessage('ai', 'Sorry, there was an error processing your message. Please try again.');
            }
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        // Auto-resize textarea
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>
