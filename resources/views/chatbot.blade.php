<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Asisten Mahad Al Yusro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0c3b2e; --primary: #1a5c48; --primary-light: #2e8b57;
            --secondary: #d4af37; --light-bg: #f4f6f8; --white: #ffffff;
            --gray-light: #e0e0e0; --text-dark: #333; --text-light: #666;
            --success: #25D366; --whatsapp: #25D366;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            color: var(--text-dark);
        }
        
        .chat-wrapper {
            width: 100%;
            max-width: 100%;
            height: 98vh;
            background: var(--white);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }
        
        /* HEADER - OPTIMIZED FOR MOBILE */
        .chat-header {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 70px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .logo {
            width: 45px;
            height: 45px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .header-info {
            flex: 1;
            min-width: 0;
        }
        
        .header-info h1 {
            font-family: 'Amiri', serif;
            font-size: 18px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .header-info p {
            font-size: 12px;
            opacity: .9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            background: rgba(255,255,255,0.15);
            padding: 5px 10px;
            border-radius: 15px;
            backdrop-filter: blur(5px);
            flex-shrink: 0;
        }
        
        .dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* MESSAGES AREA */
        .messages-container {
            flex: 1;
            overflow: hidden;
            position: relative;
            background: var(--light-bg);
        }
        
        .messages {
            height: 100%;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            -webkit-overflow-scrolling: touch;
        }
        
        .message {
            max-width: 85%;
            padding: 12px 15px;
            border-radius: 15px;
            animation: fadeSlide .3s ease-out;
            position: relative;
            line-height: 1.4;
            font-size: 14px;
            word-break: break-word;
        }
        
        .user {
            align-self: flex-end;
            background: var(--primary);
            color: var(--white);
            border-bottom-right-radius: 5px;
        }
        
        .bot {
            align-self: flex-start;
            background: var(--white);
            color: var(--text-dark);
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,.1);
        }
        
        .meta {
            font-size: 11px;
            opacity: .8;
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        /* TYPING INDICATOR IN MESSAGES AREA */
        .typing-indicator {
            display: none;
            align-self: flex-start;
            background: var(--white);
            padding: 12px 15px;
            border-radius: 15px;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,.1);
            margin-bottom: 5px;
            width: fit-content;
            animation: fadeSlide .3s ease-out;
        }
        
        .typing-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .typing-dots {
            display: flex;
            gap: 4px;
        }
        
        .typing-dots span {
            width: 6px;
            height: 6px;
            background: var(--text-light);
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out both;
        }
        
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        
        .typing-text {
            font-size: 12px;
            color: var(--text-light);
            font-weight: 500;
        }
        
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
        
        /* WHATSAPP BUTTON STYLE */
        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--whatsapp);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 500;
            margin: 5px 0;
            font-size: 14px;
            width: fit-content;
        }
        
        /* INPUT AREA - CLEAN DESIGN */
        .input-area {
            padding: 12px 15px;
            border-top: 1px solid var(--gray-light);
            background: var(--white);
            position: sticky;
            bottom: 0;
            z-index: 100;
        }
        
        .input-wrapper {
            display: flex;
            align-items: center;
            background: var(--light-bg);
            border-radius: 25px;
            padding: 5px 5px 5px 15px;
            border: 1px solid var(--gray-light);
            transition: all 0.3s ease;
        }
        
        .input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(26, 92, 72, 0.1);
        }
        
        .input-wrapper input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 15px;
            outline: none;
            font-family: 'Poppins', sans-serif;
            padding: 10px 0;
            line-height: 1.4;
            min-height: 24px;
        }
        
        /* SEND BUTTON */
        .send-btn {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 50%;
            border: none;
            background: var(--primary);
            color: var(--white);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
            transition: all 0.2s;
            margin-left: 5px;
        }
        
        .send-btn:hover,
        .send-btn:active {
            background: var(--primary-dark);
            transform: scale(0.95);
        }
        
        .send-btn:disabled {
            background: var(--gray-light);
            color: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }
        
        /* Send button animation */
        @keyframes sendPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .send-btn.pulse {
            animation: sendPulse 0.3s ease;
        }
        
        /* FOOTER */
        .footer {
            text-align: center;
            font-size: 11px;
            padding: 10px;
            background: var(--light-bg);
            color: var(--text-light);
            border-top: 1px solid var(--gray-light);
        }
        
        /* NOTIFICATION PERMISSION BANNER */
        .permission-banner {
            position: fixed;
            bottom: 15px;
            left: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
            animation: slideUp 0.3s ease-out;
        }
        
        .permission-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .permission-text h3 {
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .permission-text p {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .permission-buttons {
            display: flex;
            gap: 8px;
        }
        
        .permission-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            font-size: 14px;
        }
        
        .allow-btn {
            background: white;
            color: var(--primary);
        }
        
        .deny-btn {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        /* SCROLLBAR STYLING */
        .messages::-webkit-scrollbar {
            width: 4px;
        }
        
        .messages::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        
        .messages::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 2px;
        }
        
        /* ANIMATIONS */
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* RESPONSIVE DESIGN */
        @media (min-width: 768px) {
            .chat-wrapper {
                max-width: 800px;
                height: 90vh;
            }
            
            .header-info h1 {
                font-size: 22px;
            }
            
            .message {
                max-width: 70%;
                font-size: 15px;
            }
            
            .input-area {
                padding: 15px 20px;
            }
            
            .input-wrapper {
                padding: 8px 8px 8px 18px;
            }
            
            .input-wrapper input {
                font-size: 16px;
            }
            
            .send-btn {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }
        }
        
        @media (max-width: 380px) {
            .chat-header {
                padding: 12px 15px;
            }
            
            .logo {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            
            .header-info h1 {
                font-size: 16px;
            }
            
            .message {
                max-width: 90%;
                padding: 10px 12px;
                font-size: 13px;
            }
            
            .typing-indicator {
                padding: 10px 12px;
            }
            
            .input-area {
                padding: 10px 12px;
            }
            
            .input-wrapper {
                padding: 4px 4px 4px 12px;
            }
            
            .input-wrapper input {
                font-size: 14px;
                padding: 8px 0;
            }
            
            .send-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
        
        /* Untuk landscape mode */
        @media (max-height: 500px) and (orientation: landscape) {
            .input-area {
                padding: 8px 10px;
            }
            
            .input-wrapper {
                min-height: 40px;
            }
            
            .send-btn {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .input-wrapper {
                background: #ffffff;
                border-color: #ffffff;
                box-shadow: 0 0 0 2px rgba(26, 92, 72, 0.1);
            }
            
            .input-wrapper:focus-within {
                border-color: var(--primary-light);
            }
            
            .input-wrapper input {
                color: #363636;
            }
            
            .typing-indicator {
                background: #ffffff;
                box-shadow: 0 0 0 2px rgba(26, 92, 72, 0.1);
            }
            
            .typing-text {
                color: #2e2d2d;
            }
        }
        
        /* Fix untuk iOS Safari */
        @supports (-webkit-touch-callout: none) {
            .chat-wrapper {
                height: -webkit-fill-available;
            }
            
            .input-wrapper input {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<div class="chat-wrapper">
    <!-- HEADER -->
    <div class="chat-header">
        <div class="header-left">
            <div class="logo">
                <i class="fas fa-mosque"></i>
            </div>
            <div class="header-info">
                <h1>Asisten Mahad Al Yusro</h1>
                <p>Chat Informasi Mahad</p>
            </div>
        </div>
        <div class="status">
            <div class="dot"></div>
            <span id="status-text">Online</span>
        </div>
    </div>
    
    <!-- MESSAGES -->
    <div class="messages-container">
        <div class="messages" id="messages">
            <!-- Typing indicator will appear here -->
        </div>
    </div>
    
    <!-- INPUT AREA -->
    <div class="input-area">
        <div class="input-wrapper">
            <input type="text" id="input" placeholder="Tulis pesan..." autocomplete="off">
            <button class="send-btn" id="sendButton" onclick="sendMessage()" aria-label="Kirim pesan">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
    
    <!-- FOOTER -->
    <div class="footer">
        <p id="footer-text">Chatbot ini dikembangkan untuk tugas mata kuliah Data Mining oleh Yudi Saeful Rizal - 251012000151.</p>
        <p id="footer-text">Segala informasi yang diberikan bersifat simulasi.</p>
    </div>
</div>

<!-- NOTIFICATION BANNER -->
<div class="permission-banner" id="permissionBanner">
    <div class="permission-content">
        <div class="permission-text">
            <h3><i class="fas fa-bell"></i> Izinkan Notifikasi</h3>
            <p>Dapatkan pemberitahuan balasan pesan</p>
        </div>
        <div class="permission-buttons">
            <button class="permission-btn deny-btn" onclick="denyNotifications()">Nanti</button>
            <button class="permission-btn allow-btn" onclick="requestNotificationPermission()">Izinkan</button>
        </div>
    </div>
</div>

<script>
// ==================== KONFIGURASI ====================
const CONFIG = {
    WEBHOOK_URL: "https://n8n-idw9vfy3qzrq.arman.sumopod.my.id/webhook/d4bfa857-4667-4d0f-ad67-7d80dbd9d624",
    SESSION_KEY: 'mahad_chat_session',
    HISTORY_KEY: 'mahad_chat_history'
};

// ==================== STATE MANAGEMENT ====================
const state = {
    sessionId: null,
    messageCount: 0,
    isTyping: false,
    isConnected: true
};

// ==================== DOM ELEMENTS ====================
const elements = {
    messages: document.getElementById('messages'),
    input: document.getElementById('input'),
    sendButton: document.getElementById('sendButton'),
    statusText: document.getElementById('status-text'),
    sessionInfo: document.getElementById('session-info'),
    permissionBanner: document.getElementById('permissionBanner'),
    footerText: document.getElementById('footer-text')
};

// ==================== TYPING INDICATOR ====================
function createTypingIndicator() {
    const typingEl = document.createElement('div');
    typingEl.className = 'typing-indicator';
    typingEl.id = 'typingIndicator';
    typingEl.innerHTML = `
        <div class="typing-content">
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="typing-text">Mengetik...</div>
        </div>
    `;
    return typingEl;
}

function showTyping() {
    state.isTyping = true;
    let typingIndicator = document.getElementById('typingIndicator');
    
    if (!typingIndicator) {
        typingIndicator = createTypingIndicator();
        elements.messages.appendChild(typingIndicator);
    }
    
    typingIndicator.style.display = 'block';
    elements.sendButton.disabled = true;
    scrollToBottom();
}

function hideTyping() {
    state.isTyping = false;
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'none';
    }
    elements.sendButton.disabled = false;
}

// ==================== INITIALIZATION ====================
function init() {
    // Initialize session
    state.sessionId = localStorage.getItem(CONFIG.SESSION_KEY) || 
                      `session_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    localStorage.setItem(CONFIG.SESSION_KEY, state.sessionId);
    
    // Load chat history
    loadChatHistory();
    
    // Update UI
    elements.sessionInfo.textContent = `ID: ${state.sessionId.substring(0, 10)}...`;
    elements.footerText.textContent = `Tugas Data Mining - ${state.messageCount} pesan`;
    
    // Setup event listeners
    setupEventListeners();
    
    // Show welcome message if empty
    if (elements.messages.children.length === 0) {
        showWelcomeMessage();
    }
    
    // Auto-focus input
    setTimeout(() => elements.input.focus(), 500);
    
    // Check notification permission
    checkNotifications();
}

// ==================== CHAT HISTORY ====================
function loadChatHistory() {
    const history = localStorage.getItem(CONFIG.HISTORY_KEY);
    if (!history) return;
    
    try {
        const data = JSON.parse(history);
        data.messages.forEach(msg => {
            addMessageToUI(msg.sender, msg.text, msg.time, false);
            if (msg.sender === 'user') state.messageCount++;
        });
        scrollToBottom();
    } catch (error) {
        console.error('Error loading history:', error);
    }
}

function saveChatHistory() {
    const messages = [];
    document.querySelectorAll('.message').forEach(msg => {
        messages.push({
            sender: msg.classList.contains('user') ? 'user' : 'bot',
            text: msg.querySelector('p').textContent,
            time: msg.querySelectorAll('.meta span')[1].textContent
        });
    });
    
    const history = {
        timestamp: new Date().toISOString(),
        sessionId: state.sessionId,
        messageCount: state.messageCount,
        messages: messages
    };
    
    localStorage.setItem(CONFIG.HISTORY_KEY, JSON.stringify(history));
}

// ==================== MESSAGE HANDLING ====================
function showWelcomeMessage() {
    const welcomeText = "Assalamu'alaikum warahmatullahi wabarakatuh. \n\nAda yang bisa kami bantu, Abu/Ummu?";
    addMessageToUI('bot', welcomeText, getCurrentTime(), false);
}

function formatMessage(text) {
    // Basic formatting
    return text
        .replace(/\n/g, '<br>')
        .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>')
        .replace(/(\+62|62|0)[\s-]?\d{2,4}[\s-]?\d{3,4}[\s-]?\d{3,5}/g, match => {
            const clean = match.replace(/\D/g, '');
            const whatsappNum = clean.startsWith('0') ? '62' + clean.slice(1) : 
                              clean.startsWith('62') ? clean : '62' + clean;
            return `<a href="https://wa.me/${whatsappNum}" class="whatsapp-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i> ${match}</a>`;
        });
}

function addMessageToUI(sender, text, time = null, save = true) {
    const messageEl = document.createElement('div');
    messageEl.className = `message ${sender}`;
    
    const senderName = sender === 'user' ? 'Anda' : 'Asisten Mahad';
    const displayTime = time || getCurrentTime();
    
    messageEl.innerHTML = `
        <div class="meta">
            <span>${senderName}</span>
            <span>${displayTime}</span>
        </div>
        <p>${sender === 'bot' ? formatMessage(text) : text}</p>
    `;
    
    // Remove typing indicator before adding new message
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
    
    elements.messages.appendChild(messageEl);
    scrollToBottom();
    
    if (save) {
        setTimeout(saveChatHistory, 100);
    }
    
    return messageEl;
}

function addMessage(sender, text) {
    addMessageToUI(sender, text);
    
    if (sender === 'user') {
        state.messageCount++;
        updateFooterText();
    }
}

// ==================== SEND MESSAGE ====================
async function sendMessage() {
    const text = elements.input.value.trim();
    if (!text || state.isTyping) return;
    
    // Add animation to send button
    animateSendButton();
    
    // Clear input
    elements.input.value = '';
    
    // Add user message
    addMessage('user', text);
    
    // Show typing indicator
    showTyping();
    
    try {
        // Send to webhook
        const response = await fetch(CONFIG.WEBHOOK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Session-ID': state.sessionId
            },
            body: JSON.stringify({
                session_id: state.sessionId,
                message: text,
                timestamp: new Date().toISOString()
            })
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const reply = await response.text();
        
        // Simulate typing delay
        setTimeout(() => {
            hideTyping();
            addMessage('bot', reply);
            
            // Show notification if in background
            if (document.hidden && Notification.permission === 'granted') {
                showBrowserNotification(reply.substring(0, 100));
            }
        }, 800);
        
    } catch (error) {
        console.error('Error:', error);
        hideTyping();
        
        // Show error message
        setTimeout(() => {
            addMessage('bot', 'Maaf, terjadi kesalahan. Silakan coba lagi atau periksa koneksi internet Anda.');
        }, 800);
    }
}

// ==================== UI HELPERS ====================
function animateSendButton() {
    elements.sendButton.classList.add('pulse');
    setTimeout(() => {
        elements.sendButton.classList.remove('pulse');
    }, 300);
}

function scrollToBottom() {
    setTimeout(() => {
        elements.messages.scrollTop = elements.messages.scrollHeight;
    }, 100);
}

function getCurrentTime() {
    return new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function updateFooterText() {
    elements.footerText.textContent = `Tugas Data Mining - ${state.messageCount} pesan`;
}

// ==================== NOTIFICATIONS ====================
function checkNotifications() {
    if (Notification.permission === 'default') {
        setTimeout(() => {
            elements.permissionBanner.style.display = 'block';
        }, 3000);
    }
}

function requestNotificationPermission() {
    if (!('Notification' in window)) return;
    
    Notification.requestPermission().then(permission => {
        elements.permissionBanner.style.display = 'none';
        if (permission === 'granted') {
            showBrowserNotification('Notifikasi diaktifkan!');
        }
    });
}

function denyNotifications() {
    elements.permissionBanner.style.display = 'none';
    localStorage.setItem('notifications_denied', 'true');
}

function showBrowserNotification(message) {
    if (Notification.permission !== 'granted') return;
    
    new Notification('Asisten Mahad', {
        body: message,
        icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="%231a5c48"/><path d="M30 40h40v5H30zm0 10h30v5H30zm0 10h35v5H30z" fill="white"/></svg>'
    });
}

// ==================== EVENT LISTENERS ====================
function setupEventListeners() {
    // Send on Enter
    elements.input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Auto-resize input (if using textarea)
    elements.input.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 60) + 'px';
    });
    
    // Send button click
    elements.sendButton.addEventListener('click', sendMessage);
    
    // Save history before unload
    window.addEventListener('beforeunload', saveChatHistory);
    
    // Auto-save every 30 seconds
    setInterval(saveChatHistory, 30000);
    
    // Clear chat on header double click
    document.querySelector('.chat-header').addEventListener('dblclick', () => {
        if (confirm('Hapus semua pesan?')) {
            localStorage.removeItem(CONFIG.HISTORY_KEY);
            elements.messages.innerHTML = '';
            state.messageCount = 0;
            showWelcomeMessage();
            updateFooterText();
        }
    });
    
    // Handle virtual keyboard on mobile
    window.addEventListener('resize', () => {
        if (document.activeElement === elements.input) {
            scrollToBottom();
        }
    });
}

// ==================== START APP ====================
// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// Mobile-specific optimizations
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    // Prevent zoom on input focus for iOS
    elements.input.addEventListener('focus', function() {
        setTimeout(() => {
            this.style.fontSize = '16px';
        }, 100);
    });
}
</script>
</body>
</html>