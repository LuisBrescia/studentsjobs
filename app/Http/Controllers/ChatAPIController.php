<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatAPIController extends Controller
{
    public function getUserSessionsWithLastMessages(Request $request)
    {
        $userId = $request->user()->id;

        $sessions = ChatSession::with(['lastMessage', 'messages', 'vaga', 'empresa'])
            ->whereHas('messages', function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->get();

        return response()->json($sessions);
    }

    public function getMessagesForSession($id, Request $request)
    {
        $userId = $request->user()->id;

        $session = ChatSession::with(['messages' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        if (
            $session->estudante_id !== $userId &&
            $session->empresa_id !== $userId
        ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($session);
    }
}
