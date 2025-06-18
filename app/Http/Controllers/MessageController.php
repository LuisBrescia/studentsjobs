<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    // Listar mensagens de uma sessão de chat
    public function index($sessionId)
    {
        $userId = Auth::id();
        $session = ChatSession::findOrFail($sessionId);

        if (!in_array($userId, [$session->empresa_id, $session->estudante_id])) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $messages = Message::where('chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }



    public function store(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userId = Auth::id();
        $session = ChatSession::findOrFail($sessionId);

        if (!in_array($userId, [$session->empresa_id, $session->estudante_id])) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $receiverId = ($userId === $session->empresa_id)
            ? $session->estudante_id
            : $session->empresa_id;

        $message = Message::create([
            'chat_session_id' => $sessionId,
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }





    public function sendMessage(Request $request)
    {
        try {
            Log::info('Iniciando sendMessage', ['payload' => $request->all()]);

            $messages = [
                'chat_session_id.required' => 'A sessão de chat é obrigatória.',
                'chat_session_id.exists' => 'Sessão de chat não encontrada.',
                'receiver_id.required' => 'O destinatário é obrigatório.',
                'receiver_id.exists' => 'Destinatário não encontrado.',
                'content.required' => 'A mensagem é obrigatória.',
                'content.string' => 'A mensagem deve ser um texto.',
            ];

            $validated = $request->validate([
                'chat_session_id' => 'required|exists:chat_sessions,id',
                'receiver_id' => 'required|exists:usuarios,id',
                'content' => 'required|string',
            ], $messages);

            Log::info('Dados validados com sucesso', ['validados' => $validated]);

            $userId = Auth::id();
            Log::info('ID autenticado', ['auth_id' => $userId]);

            $empresa = \App\Models\Empresa::where('usuario_id', $userId)->first();
            $estudante = \App\Models\Estudante::where('usuario_id', $userId)->first();

            Log::info('Empresa ou Estudante localizados', [
                'empresa' => optional($empresa)->id,
                'estudante' => optional($estudante)->id,
            ]);

            if (isset($empresa)) {
                $userId = $empresa->id;
            } elseif (isset($estudante)) {
                $userId = $estudante->id;
            }

            $session = ChatSession::find($validated['chat_session_id']);
            Log::info('Sessão de chat buscada', ['session' => optional($session)->id]);

            if (!$session) {
                Log::warning('Sessão de chat não encontrada');
                return response()->json(['error' => 'Sessão de chat não encontrada'], 404);
            }

            if (!in_array($userId, [$session->empresa_id, $session->estudante_id])) {
                Log::warning('Usuário não pertence à sessão', ['user_id' => $userId, 'session' => $session->id]);
                return response()->json(['error' => 'Acesso negado, usuário não pertence a essa sessão'], 403);
            }

            $message = Message::create([
                'chat_session_id' => $validated['chat_session_id'],
                'sender_id' => $userId,
                'receiver_id' => $validated['receiver_id'],
                'content' => $validated['content'],
            ]);

            Log::info('Mensagem criada', ['message_id' => $message->id]);

            broadcast(new MessageSent($message))->toOthers();
            Log::info('Mensagem broadcasted com sucesso');

            return response()->json($message, 201);
        } catch (ValidationException $e) {
            Log::error('Erro de validação', ['erros' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Erro inesperado em sendMessage', [
                'mensagem' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
            ], 500);
        }
    }


    private function findMessageBySenderId($senderId)
    {
        $messages = Message::where('sender_id', $senderId)->get();

        if ($messages->isEmpty()) {
            return response()->json(['message' => 'Nenhuma mensagem encontrada'], 404);
        }

        return response()->json($messages);
    }

    private function findMessageByReceiverId($receiverId)
    {
        $messages = Message::where('receiver_id', $receiverId)->get();

        if ($messages->isEmpty()) {
            return response()->json(['message' => 'Nenhuma mensagem encontrada'], 404);
        }

        return response()->json($messages);
    }
    private function findMessageByChatSessionId($chatSessionId)
    {
        $messages = Message::where('chat_session_id', $chatSessionId)->get();

        if ($messages->isEmpty()) {
            return response()->json(['message' => 'Nenhuma mensagem encontrada'], 404);
        }

        return response()->json($messages);
    }

    public function mountHistoryBySessionId($sessionId)
    {
        $authUser = Auth::user();
        $session = ChatSession::findOrFail($sessionId);


        // Carrega empresa e estudante relacionados ao usuário autenticado
        $authUser->load(['empresa', 'estudante']);

        // Pega o ID real da empresa ou estudante associado
        $relatedId = null;
        if ($authUser->empresa) {
            $relatedId = $authUser->empresa->id;
        } elseif ($authUser->estudante) {
            $relatedId = $authUser->estudante->id;
        }

        Log::info('Empresa ou Estudante localizados', [
            'empresa' => optional($authUser->empresa)->id,
            'estudante' => optional($authUser->estudante)->id,
        ]);

        // Se o ID não pertence à sessão, acesso negado
        if (!in_array($relatedId, [$session->empresa_id, $session->estudante_id])) {
            return response()->json([
                'error' => 'Acesso negado, usuário não pertence a essa sessão (history messages)'
            ], 403);
        }

        $messages = Message::where('chat_session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }
}
