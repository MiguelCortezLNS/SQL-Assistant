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

        $prompt = "Eres un experto en {$motor}.
        
        Debes generar exclusivamente consultas compatibles con {$motor}.
        
        IMPORTANTE:
        - NO uses sintaxis de otros motores SQL.
        - Responde únicamente con SQL válido.
        - NO uses markdown.
        - NO expliques nada.
        - NO agregues texto adicional.
        
        Schema:
        {$schema}
        
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
