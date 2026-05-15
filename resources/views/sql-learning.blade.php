<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SQL Learning</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('css/learning.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="page-layout" id="pageLayout">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <button class="sidebar-reopen-btn" onclick="toggleSidebar()" title="Abrir sidebar">☰</button>

    <aside class="chat-sidebar" id="chatSidebar">
        <div class="sidebar-header">
            <span class="sidebar-logo">📚</span>
            <span class="sidebar-brand">SQL Learning</span>
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Contraer sidebar">‹</button>
        </div>

        <button class="btn-new-chat" onclick="newChat()">+ Nuevo Chat</button>

        <p class="sidebar-section-label">Historial</p>

        <div id="chatList" class="chat-list">
            <div class="chat-empty-hint">Sin chats aún</div>
        </div>

        <div class="sidebar-footer">
            <a href="/" class="sidebar-link">← SQL Generator</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="mobile-topbar">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>
            <span class="mobile-topbar-title">SQL Learning</span>
        </div>

        <div class="main-inner">

            <div class="hero-section">
                <span class="badge-custom">SQL Learning AI</span>
                <h1 class="main-title">Aprende SQL con IA</h1>
                <p class="main-subtitle">Haz preguntas sobre SQL y bases de datos</p>
            </div>

            <div class="learning-chat-area" id="chatMessages">
                <div class="chat-empty-state">
                    <p>¡Hola! Pregúntame sobre SQL</p>
                    <p>Triggers, JOINs, índices, subconsultas…</p>
                </div>
            </div>

            <div class="question-box card custom-card p-3">
                <div class="d-flex gap-2 align-items-end">
                    <textarea
                        id="question"
                        class="form-control custom-input flex-grow-1"
                        rows="2"
                        placeholder="¿Qué es un trigger?"
                        onkeydown="handleEnter(event)"
                    ></textarea>
                    <button class="btn btn-generate" id="askBtn" onclick="askQuestion()">
                        Preguntar
                    </button>
                </div>
            </div>

        </div>
    </main>

</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
</script>
<script src="{{ asset('js/learning.js') }}"></script>

</body>
</html>
