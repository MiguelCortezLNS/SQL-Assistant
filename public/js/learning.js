async function askQuestion(){

    const question =
        document.getElementById('question').value;

    const result =
        document.getElementById('result');

    result.textContent = 'Consultando IA...';

    const response = await fetch('/ask-sql-question', {

        method:'POST',

        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]')?.content
        },

        body:JSON.stringify({
            question
        })
    });

    const data = await response.json();

    result.textContent = data.answer;
}