<?php

namespace App\Http\Controllers;

use App\Models\Vaga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VagaController extends Controller
{
    public function index()
    {
        $empresaId = request()->query('empresa_id');

        if ($empresaId) {
            $vagas = Vaga::where('empresa_id', $empresaId)->get();
        } else {
            $vagas = Vaga::all();
        }

        return response()->json($vagas);
    }

    public function show($id)
    {
        return Vaga::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'requisitos' => 'nullable|string',
            'local' => 'nullable|string',
            'salario' => 'nullable|numeric',
            'modelo_contratacao' => 'nullable|in:pj,clt',
        ]);

        $userId = Auth::user()->id;
        $empresa = \App\Models\Empresa::where('usuario_id', $userId)->first();

        $vaga = [
            'empresa_id' => $empresa->id,
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
            'requisitos' => $data['requisitos'] ?? null,
            'data_publicacao' => now(),
            'local' => $data['local'] ?? null,
            'salario' => $data['salario'] ?? null,
            'modelo_contratacao' => $data['modelo_contratacao'] ?? null,
        ];

        $vagaCriada = Vaga::create($vaga);

        return response()->json([
            'mensagem' => 'Vaga criada com sucesso',
            'vaga' => $vagaCriada,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $vaga = Vaga::findOrFail($id);
        $vaga->update($request->all());
        return $vaga;
    }

    public function destroy($id)
    {
        Vaga::destroy($id);
        return response()->json(['mensagem' => 'Vaga deletada']);
    }

    // Listar vagas da empresa autenticada
    public function vagasDaEmpresaAutenticada()
    {
        $userId = Auth::id();
        $empresa = \App\Models\Empresa::where('usuario_id', $userId)->first();
        if (!$empresa) {
            return response()->json(['error' => 'Empresa não encontrada para o usuário autenticado'], 404);
        }
        $vagas = Vaga::where('empresa_id', $empresa->id)->get();
        return response()->json($vagas);
    }
}
