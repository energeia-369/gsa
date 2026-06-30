<?php
$pageTitle = "CHAT-BOT (E.V.A.)";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/chatbot.css?v=<?php echo time(); ?>">

<div class="chatbot-wrapper w-full max-w-4xl mx-auto px-4">
    <div class="chatbot-container">
        <div class="chatbot-header">
            <div class="bot-avatar"><img src="assets/images/E.V.A. CHATBOT.png" alt="E.V.A."></div>
            <div class="bot-info">
                <h2>E.V.A.</h2>
                <p>Online</p>
            </div>
        </div>
        
        <div class="chat-window" id="chatWindow">
            <div class="message bot">
                Hi there! I am E.V.A., your virtual assistant. How can I help you today? Please select a question below.
            </div>
            
            <div class="typing-indicator" id="typingIndicator">
                <span></span><span></span><span></span>
            </div>
        </div>
        
        <div class="questions-panel">
            <div class="questions-title">Suggested Questions</div>
            <div class="questions-list" id="questionsList">
                <!-- Questions injected via JS -->
                <span style="color:#9aa0b4; font-size:0.85rem;">Loading questions...</span>
            </div>
        </div>
        
        <div class="chat-input-area">
            <input type="text" id="customQuestionInput" class="chat-input" placeholder="Type your question here..." onkeypress="handleKeyPress(event)">
            <button class="send-btn" onclick="submitCustomQuestion()">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", async function() {
    const chatWindow = document.getElementById("chatWindow");
    const questionsList = document.getElementById("questionsList");
    const typingIndicator = document.getElementById("typingIndicator");
    
    let faqs = [];
    
    // Fetch FAQs from database
    try {
        const res = await fetch("api/index.php/chatbot-faqs");
        const data = await res.json();
        if (data.success && data.data) {
            faqs = data.data;
            renderQuestions();
        } else {
            questionsList.innerHTML = `<span style="color:#ef4444; font-size:0.85rem;">Failed to load questions.</span>`;
        }
    } catch (err) {
        questionsList.innerHTML = `<span style="color:#ef4444; font-size:0.85rem;">Error loading questions.</span>`;
        console.error(err);
    }
    
    function renderQuestions() {
        questionsList.innerHTML = "";
        if (faqs.length === 0) {
            questionsList.innerHTML = `<span style="color:#9aa0b4; font-size:0.85rem;">No questions available.</span>`;
            return;
        }
        
        faqs.forEach(faq => {
            const chip = document.createElement("div");
            chip.className = "question-chip";
            chip.textContent = faq.question;
            chip.onclick = () => handleQuestionClick(faq);
            questionsList.appendChild(chip);
        });
    }
    
    window.handleQuestionClick = function(faq) {
        addUserMessage(faq.question);
        showTypingIndicator();
        
        setTimeout(() => {
            hideTypingIndicator();
            addBotMessage(faq.answer);
        }, 800);
    };
    
    window.submitCustomQuestion = async function() {
        const input = document.getElementById("customQuestionInput");
        const questionText = input.value.trim();
        if (!questionText) return;
        
        input.value = "";
        addUserMessage(questionText);
        showTypingIndicator();
        
        try {
            const res = await fetch("api/chatbot_backend.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ question: questionText })
            });
            const data = await res.json();
            
            hideTypingIndicator();
            if (data.success && data.answer) {
                addBotMessage(data.answer);
            } else {
                addBotMessage(data.message || "I'm having trouble connecting to my AI brain right now. Please try again later!");
            }
        } catch (err) {
            console.error(err);
            hideTypingIndicator();
            addBotMessage("I encountered an error while trying to process your request. Please try again.");
        }
    };
    
    window.handleKeyPress = function(e) {
        if (e.key === 'Enter') {
            submitCustomQuestion();
        }
    };
    
    function addUserMessage(text) {
        const userMsg = document.createElement("div");
        userMsg.className = "message user";
        userMsg.textContent = text;
        chatWindow.insertBefore(userMsg, typingIndicator);
        scrollToBottom();
    }
    
    function addBotMessage(htmlContent) {
        const botMsg = document.createElement("div");
        botMsg.className = "message bot";
        botMsg.innerHTML = htmlContent.replace(/\n/g, "<br>");
        chatWindow.insertBefore(botMsg, typingIndicator);
        scrollToBottom();
    }
    
    function showTypingIndicator() {
        typingIndicator.style.display = "block";
        scrollToBottom();
    }
    
    function hideTypingIndicator() {
        typingIndicator.style.display = "none";
    }
    
    function scrollToBottom() {
        requestAnimationFrame(() => {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
