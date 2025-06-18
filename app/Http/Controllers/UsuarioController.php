<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        return Usuario::all();
    }

    public function show($id)
    {
        return Usuario::findOrFail($id);
    }

    public function store(Request $request)
    {
        return Usuario::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update($request->all());
        return $usuario;
    }

    public function destroy($id)
    {
        Usuario::destroy($id);
        return response()->json(['mensagem' => 'Usuário deletado']);
    }
}
