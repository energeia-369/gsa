<?php if (basename($_SERVER['PHP_SELF']) !== 'chatbot.php'): ?>
<link rel="stylesheet" href="assets/css/chatbot.css?v=<?php echo time(); ?>">
<div class="eva-chat-widget-container">
    <div class="chatbot-container" id="evaChatbotContainer" style="display: none;">
        <div class="chatbot-header">
            <div class="bot-avatar"><img src="assets/images/E.V.A. CHATBOT.png" alt="E.V.A."></div>
            <div class="bot-info">
                <h2>E.V.A.</h2>
                <p>Online</p>
            </div>
            <button class="close-chat-btn" onclick="toggleEvaChat()">✖</button>
        </div>
        
    <div class="chatbot-body">
        <!-- Sidebar Questions Panel -->
        <div class="sidebar-questions-panel" id="evaFloatingQuestionsPanel">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <div class="questions-title" style="margin-bottom:0;">Suggested Questions</div>
                <button onclick="toggleEvaSidebar()" style="background:none; border:none; color:#c5a85c; cursor:pointer; font-size:1rem; padding:0;" title="Close FAQs">✖</button>
            </div>
            <div class="questions-list" id="evaQuestionsList">
                <span style="color:#9aa0b4; font-size:0.85rem;">Loading questions...</span>
            </div>
        </div>
        
        <div class="chat-main">
            <!-- Toggle Sidebar Button (visible when sidebar is closed) -->
            <div id="evaOpenSidebarContainer" style="display:none; padding: 15px 20px 0 20px;">
                <button onclick="toggleEvaSidebar()" style="background: rgba(201, 163, 74, 0.1); border: 1px solid rgba(201, 163, 74, 0.3); color: #c5a85c; padding: 6px 12px; border-radius: 15px; cursor:pointer; font-size: 0.8rem; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
                    <i class="fa-solid fa-list"></i> Show FAQs
                </button>
            </div>
            <div class="chat-window" id="evaChatWindow">
                <div class="message bot">
                    Hi there! I am E.V.A., your virtual assistant. How can I help you today? Please select a question below.
                </div>
                
                <div class="typing-indicator" id="evaTypingIndicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
            
            <div class="chat-input-area">
                <input type="text" id="evaCustomQuestionInput" class="chat-input" placeholder="Type your question here..." onkeypress="handleEvaKeyPress(event)">
                <button class="send-btn" onclick="submitEvaCustomQuestion()">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>


    <!-- Tooltip -->
    <div class="eva-tooltip" id="evaTooltip" style="display: none;">
        Have any questions ? Ask E.V.A. !!!
        <button class="eva-tooltip-close" onclick="closeEvaTooltip(event)">✖</button>
    </div>

    <!-- Floating Chat Button -->
    <button class="eva-floating-btn" id="evaFloatingBtn" onclick="toggleEvaChat()" title="Chat with E.V.A.">
        <img src="assets/images/E.V.A. CHATBOT.png" alt="Chat with E.V.A.">
    </button>
</div>

<script>
let evaFaqs = [];

async function loadEvaChatbot() {
    const questionsList = document.getElementById("evaQuestionsList");
    if (!questionsList) return;
    
    try {
        const res = await fetch("api/index.php/chatbot-faqs");
        const data = await res.json();
        if (data.success && data.data) {
            evaFaqs = data.data;
            renderEvaQuestions();
        } else {
            questionsList.innerHTML = `<span style="color:#ef4444; font-size:0.85rem;">Failed to load questions.</span>`;
        }
    } catch (err) {
        questionsList.innerHTML = `<span style="color:#ef4444; font-size:0.85rem;">Error loading questions.</span>`;
        console.error(err);
    }
}

function renderEvaQuestions() {
    const questionsList = document.getElementById("evaQuestionsList");
    questionsList.innerHTML = "";
    if (evaFaqs.length === 0) {
        questionsList.innerHTML = `<span style="color:#9aa0b4; font-size:0.85rem;">No questions available.</span>`;
        return;
    }
    
    evaFaqs.forEach(faq => {
        const chip = document.createElement("div");
        chip.className = "question-chip";
        chip.textContent = faq.question;
        chip.onclick = () => handleEvaQuestionClick(faq);
        questionsList.appendChild(chip);
    });
}

function handleEvaQuestionClick(faq) {
    addEvaUserMessage(faq.question);
    showEvaTypingIndicator();
    
    setTimeout(() => {
        hideEvaTypingIndicator();
        addEvaBotMessage(faq.answer);
    }, 800);
}

async function submitEvaCustomQuestion() {
    const input = document.getElementById("evaCustomQuestionInput");
    const questionText = input.value.trim();
    if (!questionText) return;
    
    input.value = "";
    addEvaUserMessage(questionText);
    showEvaTypingIndicator();
    
    try {
        const res = await fetch("api/chatbot_backend.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ question: questionText })
        });
        const data = await res.json();
        
        hideEvaTypingIndicator();
        if (data.success && data.answer) {
            addEvaBotMessage(data.answer);
        } else {
            addEvaBotMessage(data.message || "I'm having trouble connecting to my AI brain right now. Please try again later!");
        }
    } catch (err) {
        console.error(err);
        hideEvaTypingIndicator();
        addEvaBotMessage("I encountered an error while trying to process your request. Please try again.");
    }
}

function handleEvaKeyPress(e) {
    if (e.key === 'Enter') {
        submitEvaCustomQuestion();
    }
}

function addEvaUserMessage(text) {
    const chatWindow = document.getElementById("evaChatWindow");
    const userMsg = document.createElement("div");
    userMsg.className = "message user";
    userMsg.textContent = text;
    chatWindow.insertBefore(userMsg, document.getElementById("evaTypingIndicator"));
    scrollEvaToBottom();
}

function addEvaBotMessage(htmlContent) {
    const chatWindow = document.getElementById("evaChatWindow");
    const botMsg = document.createElement("div");
    botMsg.className = "message bot";
    botMsg.innerHTML = htmlContent.replace(/\n/g, "<br>");
    chatWindow.insertBefore(botMsg, document.getElementById("evaTypingIndicator"));
    scrollEvaToBottom();
}

function showEvaTypingIndicator() {
    document.getElementById("evaTypingIndicator").style.display = "block";
    scrollEvaToBottom();
}

function hideEvaTypingIndicator() {
    document.getElementById("evaTypingIndicator").style.display = "none";
}

function scrollEvaToBottom() {
    requestAnimationFrame(() => {
        const chatWindow = document.getElementById("evaChatWindow");
        if (chatWindow) {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
    });
}

function toggleEvaChat() {
    closeEvaTooltip();
    const container = document.getElementById("evaChatbotContainer");
    const questionsPanel = document.getElementById("evaFloatingQuestionsPanel");
    
    if (container.style.display === "none" || container.style.display === "") {
        container.style.display = "flex";
        container.classList.remove("hiding");
        
        if (evaFaqs.length === 0) {
            loadEvaChatbot();
        }
    } else {
        container.classList.add("hiding");
        setTimeout(() => {
            container.style.display = "none";
            container.classList.remove("hiding");
        }, 300); // Matches the animation duration
    }
}

function toggleEvaSidebar() {
    const sidebar = document.getElementById("evaFloatingQuestionsPanel");
    const openBtnContainer = document.getElementById("evaOpenSidebarContainer");
    
    if (sidebar.style.display === "none") {
        sidebar.style.display = "flex";
        openBtnContainer.style.display = "none";
    } else {
        sidebar.style.display = "none";
        openBtnContainer.style.display = "block";
    }
}

document.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
        const tooltip = document.getElementById("evaTooltip");
        const container = document.getElementById("evaChatbotContainer");
        if (tooltip && container && container.style.display === "none") {
            tooltip.style.display = "flex";
            setTimeout(() => {
                if (tooltip) tooltip.style.display = "none";
            }, 10000);
        }
    }, 5000);
});

function closeEvaTooltip(e) {
    if (e) e.stopPropagation();
    const tooltip = document.getElementById("evaTooltip");
    if (tooltip) tooltip.style.display = "none";
}
</script>
<?php endif; ?>
