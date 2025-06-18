<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::all();

        return response()->json($empresas);
    }

    public function show($id)
    {
        $empresa = Empresa::find($id);

        if (!$empresa) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }

        return response()->json($empresa);
    }
}
