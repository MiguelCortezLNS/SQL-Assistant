let currentChatId = null;

document.addEventListener('DOMContentLoaded', () => {    // Restore sidebar collapsed state on desktop
    if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === 'true') {
        document.getElementById('pageLayout').classList.add('sidebar-collapsed');
    }    loadChats();
    setupFileReader();
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
        const res = await fetch('/chats?type=sql', {
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

        const userMsg      = chat.messages.find(m => m.role === 'user');
        const assistantMsg = chat.messages.find(m => m.role === 'assistant');

        if (userMsg) {
            try {
                const data = JSON.parse(userMsg.content);
                document.getElementById('motor').value    = data.motor    || 'MySQL';
                document.getElementById('schema').value   = data.schema   || '';
                document.getElementById('question').value = data.question || '';
            } catch (_) {}
        }
        if (assistantMsg) {
            document.getElementById('result').textContent = assistantMsg.content;
        }

        loadChats();
    } catch (e) {
        console.error('Error cargando chat', e);
    }
}

function newChat() {
    currentChatId = null;
    document.getElementById('schema').value   = '';
    document.getElementById('question').value = '';
    document.getElementById('result').textContent = 'SQL generado';
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

async function generateSQL() {
    const motor    = document.getElementById('motor').value;
    const schema   = document.getElementById('schema').value;
    const question = document.getElementById('question').value;
    const result   = document.getElementById('result');
    const loading  = document.getElementById('loading');
    const button   = document.getElementById('generateBtn');

    if (!question.trim()) return;

    loading.classList.remove('d-none');
    button.disabled = true;
    result.textContent = '-- Generando SQL...';

    try {
        // Each generation creates its own chat entry
        const chatRes = await fetch('/chats', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() },
            body: JSON.stringify({ title: question.substring(0, 60), type: 'sql' })
        });
        const chat = await chatRes.json();
        currentChatId = chat.id;

        // Save user message (motor + schema + question as JSON)
        await fetch('/chats/' + currentChatId + '/messages', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() },
            body: JSON.stringify({ role: 'user', content: JSON.stringify({ motor, schema, question }) })
        });

        // Call AI
        const response = await fetch('/generate-sql', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ motor, schema, question })
        });
        const data = await response.json();
        result.textContent = data.sql;

        // Save assistant message
        await fetch('/chats/' + currentChatId + '/messages', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Browser-ID': getBrowserId() },
            body: JSON.stringify({ role: 'assistant', content: data.sql })
        });

        loadChats();
    } catch (error) {
        console.error(error);
        result.textContent = 'Error generando SQL';
    } finally {
        loading.classList.add('d-none');
        button.disabled = false;
    }
}

function copySQL() {
    const sql = document.getElementById('result').textContent;
    navigator.clipboard.writeText(sql);
    alert('SQL copiado');
}

// ─── File upload ───────────────────────────────────────────────────────────
function openFileExplorer() {
    document.getElementById('sqlFile').click();
}

function setupFileReader() {
    document.getElementById('sqlFile').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('schema').value = e.target.result; };
        reader.readAsText(file);
    });
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
function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}


document
    .getElementById('sqlFile')
    .addEventListener('change', handleFileUpload);

function handleFileUpload(event) {

    const file = event.target.files[0];

    if (!file) return;

    document.getElementById('fileName')
        .textContent = file.name;

    const reader = new FileReader();

    reader.onload = function (e) {

        const content = e.target.result;

        document.getElementById('schema').value =
            content;
    };

    reader.readAsText(file);
}