async function generateSQL() {

    const motor =
        document.getElementById('motor').value;

    const schema =
        document.getElementById('schema').value;

    const question =
        document.getElementById('question').value;

    const result =
        document.getElementById('result');

    const loading =
        document.getElementById('loading');

    const button =
        document.getElementById('generateBtn');

    loading.classList.remove('d-none');

    button.disabled = true;

    result.textContent =
        '-- Generando SQL...';

    try {

        const response = await fetch('/generate-sql', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },

            body: JSON.stringify({
                motor,
                schema,
                question
            })
        });

        const data = await response.json();

        result.textContent = data.sql;

    } catch (error) {

        console.error(error);

        result.textContent =
            'Error generando SQL';

    } finally {

        loading.classList.add('d-none');

        button.disabled = false;
    }
}

function copySQL() {

    const sql =
        document.getElementById('result').textContent;

    navigator.clipboard.writeText(sql);

    alert('SQL copiado');
}

function openFileExplorer() {

    document
        .getElementById('sqlFile')
        .click();
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