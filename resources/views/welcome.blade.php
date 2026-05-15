<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL AI Assistant</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

<div class="page-layout" id="pageLayout">

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <button class="sidebar-reopen-btn" onclick="toggleSidebar()" title="Abrir sidebar">☰</button>

    <aside class="chat-sidebar" id="chatSidebar">
        <div class="sidebar-header">
            <span class="sidebar-logo">⚙️</span>
            <span class="sidebar-brand">SQL AI</span>
            <button class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Contraer sidebar">‹</button>
        </div>

        <button class="btn-new-chat" onclick="newChat()">+ Nuevo Chat</button>

        <p class="sidebar-section-label">Historial</p>

        <div id="chatList" class="chat-list">
            <div class="chat-empty-hint">Sin chats aún</div>
        </div>

        <div class="sidebar-footer">
            <a href="/sql-learning" class="sidebar-link">📚 Aprender SQL</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="mobile-topbar">
            <button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>
            <span class="mobile-topbar-title">SQL AI Assistant</span>
        </div>

        <div class="main-inner">

            <div class="hero-section mb-4">
                <div class="badge-custom mb-3">IA + SQL GENERATOR</div>
                <h1 class="main-title">SQL AI Assistant</h1>
                <p class="main-subtitle">Genera consultas SQL usando inteligencia artificial a partir de lenguaje natural.</p>
            </div>

            <div class="card custom-card">
                <div class="card-body p-4">

                    <div class="mb-4">
                        <label class="form-label">Motor de Base de Datos</label>
                        <select id="motor" class="form-select custom-input">
                            <option>MySQL</option>
                            <option>PostgreSQL</option>
                            <option>SQL Server</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Subir archivo Schema SQL</label>
                        <div class="upload-container">
                            <input type="file" id="sqlFile" accept=".sql,.txt" hidden>
                            <button class="btn btn-upload" onclick="openFileExplorer()">Seleccionar archivo</button>
                            <span id="fileName" class="file-name">Ningún archivo seleccionado</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Esquema de Base de Datos</label>
                        <textarea id="schema" class="form-control custom-input" rows="6"
                            placeholder="usuarios(id, nombre, correo)"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Consulta en lenguaje natural</label>
                        <textarea id="question" class="form-control custom-input" rows="3"
                            placeholder="Muéstrame usuarios con más de 5 pedidos"></textarea>
                    </div>

                    <div class="d-flex gap-3 align-items-center">
                        <button class="btn btn-generate" onclick="generateSQL()" id="generateBtn">Generar SQL</button>
                        <button class="btn btn-copy" onclick="copySQL()">Copiar</button>
                        <div id="loading" class="loading-text d-none">Generando...</div>
                    </div>

                </div>
            </div>

            <div class="card custom-card result-card mt-4">
                <div class="card-body">
                    <h4 class="mb-3 text-white">Resultado SQL</h4>
                    <pre id="result">SQL generado</pre>
                </div>
            </div>

        </div>
    </main>

</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
</script>
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
