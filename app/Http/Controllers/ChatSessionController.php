<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Empresa;
use App\Models\Estudante;
use App\Models\Vaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatSessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $empresa = $user->empresa;
        $estudante = $user->estudante;

        $sessions = collect();

        if ($empresa) {
            $empresaSessions = ChatSession::where('empresa_id', $empresa->id)
                ->with('vaga', 'estudante')
                ->get();
            $sessions = $sessions->merge($empresaSessions);
        }

        if ($estudante) {
            $estudanteSessions = ChatSession::where('estudante_id', $estudante->id)
                ->with('vaga', 'empresa')
                ->get();
            $sessions = $sessions->merge($estudanteSessions);
        }

        if ($sessions->isEmpty()) {
            return response()->json(['error' => 'Nenhuma sessão encontrada para este usuário'], 404);
        }

        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaga_id' => 'required|exists:vagas,id',
            'estudante_id' => 'required|exists:estudantes,id'
        ]);

        $empresaId = Auth::user()->empresa->id;

        // * Caso a vaga não pertença à empresa autenticada, retorna erro
        $vaga = Vaga::where('id', $validated['vaga_id'])
            ->where('empresa_id', $empresaId)
            ->first();
        if (!$vaga) {
            return response()->json(['error' => 'Vaga não encontrada para essa empresa'], 404);
        }

        // * Verifica se já existe uma sessão de chat para a vaga e estudante
        $existingSession = ChatSession::where('vaga_id', $validated['vaga_id'])
            ->where('estudante_id', $validated['estudante_id'])
            ->first();
        if ($existingSession) {
            return response()->json(['error' => 'Já existe uma sessão para esta vaga com este estudante'], 409);
        }

        $chatSession = ChatSession::create([
            'vaga_id' => $validated['vaga_id'],
            'estudante_id' => $validated['estudante_id'],
            'empresa_id' => $empresaId,
        ]);

        return response()->json($chatSession, 201);
    }

    public function destroy($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->delete();

        return response()->json(['message' => 'Sessão de chat deletada com sucesso']);
    }

    public function initializeSessionsWithLastMessage($id)
    {
        $userId = $id;

        // Buscar sessões onde o usuário é estudante ou empresa
        $sessions = ChatSession::where('estudante_id', $userId)
            ->orWhere('empresa_id', $userId)
            ->with(['messages', 'vaga', 'empresa', 'estudante'])
            ->get();

        // Formatar resultado: cada sessão com suas mensagens
        $result = $sessions->map(function ($session) {
            return [
                'session' => $session,
                'messages' => $session->messages,
            ];
        });

        return response()->json($result);
    }
}
