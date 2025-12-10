// FILE: ajax/chatbot.js

export function handleChatbotSubmit(e) {
    const formData = new FormData(e.target);
    const question = formData.get('question');
    const employee_id = formData.get('employee_id');
    
    if (!question.trim()) return;
    
    addMessageToChat(question, 'user');
    
    e.target.reset();
    
    showTypingIndicator();
    
    fetch('actions/chatbot/ask.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        removeTypingIndicator();
        
        if (data.success) {
            addMessageToChat(data.answer, 'bot');
        } else {
            addMessageToChat('Sorry, I encountered an error. Please try again.', 'bot');
        }
    })
    .catch(() => {
        removeTypingIndicator();
        addMessageToChat('Sorry, something went wrong. Please try again later.', 'bot');
    });
}

export function askQuestion(question) {
    const chatInput = document.getElementById('chat-input');
    const employeeId = document.getElementById('employee_id').value;
    
    if (chatInput) {
        addMessageToChat(question, 'user');
        chatInput.value = '';
        
        showTypingIndicator();
        
        const formData = new FormData();
        formData.append('question', question);
        formData.append('employee_id', employeeId);
        
        fetch('actions/chatbot/ask.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            removeTypingIndicator();
            
            if (data.success) {
                addMessageToChat(data.answer, 'bot');
            } else {
                addMessageToChat('Sorry, I encountered an error. Please try again.', 'bot');
            }
        })
        .catch(() => {
            removeTypingIndicator();
            addMessageToChat('Sorry, something went wrong. Please try again later.', 'bot');
        });
    }
}

function addMessageToChat(message, sender) {
    const chatDisplay = document.getElementById('chat-display');
    const messageDiv = document.createElement('div');
    
    if (sender === 'user') {
        messageDiv.className = 'row-fixed justify-right';
        messageDiv.innerHTML = `<p class="secondary-status">${escapeHtml(message)}</p>`;
    } else {
        messageDiv.className = 'row-fixed justify-left';
        messageDiv.innerHTML = `<p class="secondary-status">${formatBotMessage(message)}</p>`;
    }
    
    chatDisplay.appendChild(messageDiv);
    chatDisplay.scrollTop = chatDisplay.scrollHeight;
}

function showTypingIndicator() {
    const chatDisplay = document.getElementById('chat-display');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'row-fixed justify-left';
    typingDiv.id = 'typing-indicator';
    typingDiv.innerHTML = '<p class="secondary-status">⏳ Typing...</p>';
    chatDisplay.appendChild(typingDiv);
    chatDisplay.scrollTop = chatDisplay.scrollHeight;
}

function removeTypingIndicator() {
    const typingIndicator = document.getElementById('typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

function formatBotMessage(message) {
    message = escapeHtml(message);
    message = message.replace(/\n/g, '<br>');
    message = message.replace(/•/g, '&bull;');
    return message;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

window.askQuestion = askQuestion;