<?php

namespace App\Http\Controllers;

use App\Models\Curriculo;
use Illuminate\Http\Request;

class CurriculoController extends Controller
{
    public function index()
    {
        return Curriculo::all();
    }

    public function show($id)
    {
        return Curriculo::findOrFail($id);
    }

    public function store(Request $request)
    {
        return Curriculo::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $curriculo = Curriculo::findOrFail($id);
        $curriculo->update($request->all());
        return $curriculo;
    }

    public function destroy($id)
    {
        Curriculo::destroy($id);
        return response()->json(['mensagem' => 'Currículo deletado']);
    }

    public function getCurriculosByUsuario($usuarioId)
    {
        return Curriculo::where('usuario_id', $usuarioId)->get();
    }

    public function getCurriculosByVaga($vagaId)
    {
        return Curriculo::where('vaga_id', $vagaId)->get();
    }

    public function getCurriculosByStatus($status)
    {
        return Curriculo::where('status', $status)->get();
    }
}
