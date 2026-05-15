<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    private function browserId(Request $request): string
    {
        $id = $request->header('X-Browser-ID');
        return ($id && strlen($id) >= 8) ? $id : $request->session()->getId();
    }

    public function index(Request $request)
    {
        $type = $request->query('type', 'sql');

        $chats = Chat::forSession($this->browserId($request))
            ->where('type', $type)
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'title', 'updated_at']);

        return response()->json($chats);
    }

    public function store(Request $request)
    {
        $chat = Chat::create([
            'title'      => $request->input('title', 'Nuevo chat'),
            'type'       => $request->input('type', 'sql'),
            'session_id' => $this->browserId($request),
        ]);

        return response()->json($chat);
    }

    public function show(Request $request, Chat $chat)
    {
        if ($chat->session_id !== $this->browserId($request)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json($chat->load('messages'));
    }

    public function storeMessage(Request $request, Chat $chat)
    {
        if ($chat->session_id !== $this->browserId($request)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $message = $chat->messages()->create([
            'role'    => $request->input('role'),
            'content' => $request->input('content'),
        ]);

        $chat->touch();

        return response()->json($message);
    }

    public function destroy(Request $request, Chat $chat)
    {
        if ($chat->session_id !== $this->browserId($request)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $chat->delete();

        return response()->json(['ok' => true]);
    }
}
