<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LearningController extends Controller
{
    public function index()
    {
        return view('sql-learning');
    }

    public function ask(Request $request)
    {
        $question = $request->question;

        $prompt = "
        Eres un experto en SQL y bases de datos.

        Explica de forma sencilla la siguiente pregunta.

        Incluye:
        - explicación clara
        - ejemplo práctico SQL

        NO uses markdown.

        Pregunta:
        {$question}
        ";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [

            'model' => 'llama-3.3-70b-versatile',

            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],

            'temperature' => 0.3
        ]);

        $data = $response->json();

        $answer = $data['choices'][0]['message']['content'];

        return response()->json([
            'answer' => $answer
        ]);
    }
}