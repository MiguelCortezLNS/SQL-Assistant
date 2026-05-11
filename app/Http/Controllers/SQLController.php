<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SQLController extends Controller
{
    public function generate(Request $request)
    {
        $motor = $request->motor;
        $schema = $request->schema;
        $question = $request->question;

        $prompt = " Eres un experto en {$motor}.
        
        Esquema: {$schema}
        
        Genera únicamente SQL válido.
        No expliques nada.
        No uses markdown.
        
        Pregunta:
        {$question}";

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

            'temperature' => 0.2
        ]);

        // DEBUG
        // return response()->json($response->json());

        $data = $response->json();

        // Validar si hubo error
        if (isset($data['error'])) {
            return response()->json([
                'sql' => 'ERROR OPENAI: ' . $data['error']['message']
            ]);
        }

        $sql = $data['choices'][0]['message']['content'];

        $sql = str_replace('```sql', '', $sql);

        $sql = str_replace('```', '', $sql);

        $sql = trim($sql);

        return response()->json([
            'sql' => $sql
        ]);
    }
}
