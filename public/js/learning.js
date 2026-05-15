let currentChatId = null;

document.addEventListener('DOMContentLoaded', () => {    if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === 'true') {
        document.getElementById('pageLayout').classList.add('sidebar-collapsed');
    }    loadChats();
});

function toggleSidebar() {
    const isMobile = window.innerWidth <= 768;
    if (isMobile) {
        document.getElementById('chatSidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    } else {
        const layout    = document.getElementById('pageLayout');
        const collapsed = layout.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', collapsed);
    }
}

function closeSidebar() {
    document.getElementById('chatSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('active');
}

async function loadChats() {
    try {
        const res = await fetch('/chats?type=learning', {
            headers: { 'X-Browser-ID': getBrowserId() }
        });
        const chats = await res.json();
        renderChatList(chats);
    } catch (e) {
        console.error('Error cargando chats', e);
    }
}

function renderChatList(chats) {
    const list = document.getElementById('chatList');
    if (!chats.length) {
        list.innerHTML = '<div class="chat-empty-hint">Sin chats aún</div>';
        return;
    }
    list.innerHTML = '';
    chats.forEach(chat => {
        const item = document.createElement('div');
        item.className = 'chat-item' + (chat.id === currentChatId ? ' active' : '');
        item.dataset.chatId = chat.id;
        item.innerHTML = `
            <span class="chat-item-title">${escapeHtml(chat.title)}</span>
            <button class="chat-item-delete" onclick="deleteChat(event, ${chat.id})">✕</button>
        `;
        item.addEventListener('click', () => loadChat(chat.id));
        list.appendChild(item);
    });
}

async function loadChat(chatId) {
    try {
        const res = await fetch('/chats/' + chatId, {
            headers: { 'X-Browser-ID': getBrowserId() }
        });
        const chat = await res.json();
        currentChatId = chatId;
        renderMessages(chat.messages);
        loadChats();
    } catch (e) {
        console.error('Error cargando chat', e);
    }
}

function newChat() {
    currentChatId = null;
    document.getElementById('question').value = '';
    document.getElementById('chatMessages').innerHTML = `
        <div class="chat-empty-state">
            <p>👋 ¡Hola! Pregúntame sobre SQL</p>
            <p>Triggers, JOINs, índices, subconsultas…</p>
        </div>
    `;
    loadChats();
}

async function deleteChat(e, chatId) {
    e.stopPropagation();
    try {
        await fetch('/chats/' + chatId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() }
        });
        if (currentChatId === chatId) newChat();
        else loadChats();
    } catch (err) {
        console.error('Error eliminando chat', err);
    }
}

function renderMessages(messages) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';
    messages.forEach(msg => appendMessage(msg.role, msg.content));
    container.scrollTop = container.scrollHeight;
}

function appendMessage(role, content) {
    const container = document.getElementById('chatMessages');
    const emptyState = container.querySelector('.chat-empty-state');
    if (emptyState) emptyState.remove();

    const bubble = document.createElement('div');
    bubble.className = 'msg-bubble msg-' + role;
    bubble.innerHTML = `
        <div class="msg-label">${role === 'user' ? 'Tú' : 'AI'}</div>
        <div class="msg-content">${role === 'assistant' ? formatAnswer(content) : escapeHtml(content)}</div>
    `;
    container.appendChild(bubble);
    container.scrollTop = container.scrollHeight;
}

async function askQuestion() {
    const question = document.getElementById('question').value.trim();
    if (!question) return;

    const btn = document.getElementById('askBtn');
    btn.disabled = true;
    document.getElementById('question').value = '';

    appendMessage('user', question);

    const thinkingId = 'thinking-' + Date.now();
    const container  = document.getElementById('chatMessages');
    const thinking   = document.createElement('div');
    thinking.id        = thinkingId;
    thinking.className = 'msg-bubble msg-assistant msg-thinking';
    thinking.innerHTML = '<div class="msg-label">AI</div><div class="msg-content">Consultando IA…</div>';
    container.appendChild(thinking);
    container.scrollTop = container.scrollHeight;

    try {
        if (!currentChatId) {
            const chatRes = await fetch('/chats', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() },
                body: JSON.stringify({ title: question.substring(0, 60), type: 'learning' })
            });
            const chat = await chatRes.json();
            currentChatId = chat.id;
        }

        await fetch('/chats/' + currentChatId + '/messages', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() },
            body: JSON.stringify({ role: 'user', content: question })
        });

        const response = await fetch('/ask-sql-question', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ question })
        });
        const data = await response.json();

        document.getElementById(thinkingId)?.remove();
        appendMessage('assistant', data.answer);

        await fetch('/chats/' + currentChatId + '/messages', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() },
            body: JSON.stringify({ role: 'assistant', content: data.answer })
        });

        loadChats();
    } catch (error) {
        console.error(error);
        document.getElementById(thinkingId)?.remove();
        appendMessage('assistant', 'Error consultando la IA. Intenta de nuevo.');
    } finally {
        btn.disabled = false;
    }
}

function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        askQuestion();
    }
}

function getBrowserId() {
    let id = localStorage.getItem('browser_id');
    if (!id) {
        id = ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
        localStorage.setItem('browser_id', id);
    }
    return id;
}

function formatAnswer(text) {
    return escapeHtml(text).replace(/\n/g, '<br>');
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
