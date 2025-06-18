<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Candidatura;

class CandidaturaController extends Controller
{
    public function index()
    {
        $estudanteId = Auth::user()->estudante->id;
        return Candidatura::where('estudante_id', $estudanteId)->get();
    }

    public function show($id)
    {
        return Candidatura::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaga_id' => 'required|exists:vagas,id',
        ]);

        $estudanteId = Auth::user()->estudante->id;

        $existe = Candidatura::where('vaga_id', $validated['vaga_id'])
            ->where('estudante_id', $estudanteId)
            ->exists();

        if ($existe) {
            return response()->json(['error' => 'Você já se candidatou a esta vaga.'], 409);
        }

        $candidatura = Candidatura::create([
            'vaga_id' => $validated['vaga_id'],
            'estudante_id' => $estudanteId,
            'status' => 'pendente'
        ]);

        return response()->json($candidatura, 201);
    }

    public function update(Request $request, $id)
    {
        $candidatura = Candidatura::findOrFail($id);
        $candidatura->update($request->all());
        return $candidatura;
    }

    public function destroy($id)
    {
        Candidatura::destroy($id);
        return response()->json(['mensagem' => 'Candidatura deletada']);
    }

    public function getCandidaturasEmpresaAutenticada()
    {
        $vagaId = request()->query('vaga_id');
        $empresaId = Auth::user()->empresa->id;

        if ($vagaId) {
            return Candidatura::where('vaga_id', $vagaId)
                ->whereHas('vaga', function ($query) use ($empresaId) {
                    $query->where('empresa_id', $empresaId);
                })->get();
        }

        return Candidatura::whereHas('vaga', function ($query) use ($empresaId) {
            $query->where('empresa_id', $empresaId);
        })->get();
    }
}
