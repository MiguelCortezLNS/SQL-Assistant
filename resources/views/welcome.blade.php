<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SQL AI Assistant</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <div class="main-wrapper">

        <div class="container py-5">

            <div class="hero-section mb-5">

                <div class="badge-custom mb-3">
                    IA + SQL GENERATOR
                </div>

                <h1 class="main-title">
                    SQL AI Assistant
                </h1>

                <p class="main-subtitle">
                    Genera consultas SQL usando inteligencia artificial
                    a partir de lenguaje natural.
                </p>

            </div>

            <div class="card custom-card">
                <a href="/sql-learning" class="btn btn-upload">
                    Aprender SQL
                </a>

                <div class="card-body p-4">

                    <div class="mb-4">

                        <label class="form-label">
                            Motor de Base de Datos
                        </label>

                        <select id="motor" class="form-select custom-input">

                            <option>MySQL</option>

                            <option>PostgreSQL</option>

                            <option>SQL Server</option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Subir archivo Schema SQL
                        </label>

                        <div class="upload-container">

                            <input type="file" id="sqlFile" accept=".sql,.txt" hidden>

                            <button class="btn btn-upload" onclick="openFileExplorer()">
                                Seleccionar archivo
                            </button>

                            <span id="fileName" class="file-name">
                                Ningún archivo seleccionado
                            </span>

                        </div>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Esquema de Base de Datos
                        </label>

                        <textarea id="schema" class="form-control custom-input" rows="8" placeholder="usuarios(id, nombre, correo)"></textarea>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Consulta en lenguaje natural
                        </label>

                        <textarea id="question" class="form-control custom-input" rows="4"
                            placeholder="Muéstrame usuarios con más de 5 pedidos"></textarea>

                    </div>

                    <div class="d-flex gap-3">

                        <button class="btn btn-generate" onclick="generateSQL()" id="generateBtn">
                            Generar SQL
                        </button>

                        <button class="btn btn-copy" onclick="copySQL()">
                            Copiar
                        </button>

                    </div>

                </div>

            </div>

            <div class="card custom-card result-card mt-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h4 class="mb-0 text-white">
                            Resultado SQL
                        </h4>

                        <div id="loading" class="loading-text d-none">
                            Generando...
                        </div>

                    </div>

                    <pre id="result">SQL generado
                </pre>

                </div>

            </div>

        </div>

    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';
    </script>

    <script src="{{ asset('js/app.js') }}"></script>

</body>

</html>
