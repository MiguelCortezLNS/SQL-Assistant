<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>SQL Learning</title>

    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    >
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap"
    >
    <link
        rel="stylesheet"
        href="{{ asset('css/learning.css') }}"
    >
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="container main-wrapper">

    <div class="mb-4">
        <a href="{{ url('/') }}" class="btn-return">
            &#8592; Regresar
        </a>
    </div>

    <div class="hero-section mb-5">

        <span class="badge-custom">
            SQL Learning AI
        </span>

        <h1 class="main-title">
            Aprende SQL con IA
        </h1>

        <p class="main-subtitle">
            Haz preguntas sobre SQL y bases de datos
        </p>

    </div>

    <div class="card custom-card p-4">

        <div class="mb-4">

            <label class="form-label">
                Pregunta
            </label>

            <textarea
                id="question"
                class="form-control custom-input"
                rows="4"
                placeholder="¿Qué es un trigger?"
            ></textarea>

        </div>

        <button
            class="btn btn-generate"
            onclick="askQuestion()"
        >
            Preguntar
        </button>

    </div>

    <div class="card custom-card p-4 mt-4 result-card">

        <h3 class="mb-3">
            Respuesta
        </h3>

        <pre id="result"></pre>

    </div>

</div>

<script src="{{ asset('js/learning.js') }}"></script>

</body>
</html>