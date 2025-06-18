<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudante;
use App\Models\Curriculo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstudanteController extends Controller
{
    public function index()
    {
        $estudantes = Estudante::all();

        return response()->json($estudantes);
    }

    public function show($id)
    {
        $estudante = Estudante::find($id);

        if (!$estudante) {
            return response()->json(['message' => 'Estudante não encontrado'], 404);
        }

        return response()->json($estudante);
    }

    public function salvarCurriculo(Request $request)
    {
        try {
            // Validação dos dados (recomendado)
            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'email' => 'required|email',
            ]);

            $user = Auth::user();

            DB::beginTransaction();

            $estudante = $user->estudante()->firstOrCreate(['usuario_id' => $user->id]);
            if ($estudante->curriculo_obj_id) {
                Curriculo::where('_id', $estudante->curriculo_obj_id)->delete();
            }

            $curriculo = Curriculo::create($request->all());
            $estudante->curriculo_obj_id = (string) $curriculo->_id;
            $estudante->save();

            DB::commit();

            return response()->json([
                'message' => 'Currículo salvo com sucesso',
                'data' => $curriculo
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao salvar currículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function carregarCurriculo(Request $request)
    {
        try {
            $user = Auth::user();

            $estudante = $user->estudante;
            if (!$estudante || !$estudante->curriculo_obj_id) {
                return response()->json([
                    'message' => 'Nenhum currículo encontrado para este usuário'
                ], 404);
            }

            $curriculo = Curriculo::findOrFail($estudante->curriculo_obj_id);

            return response()->json([
                'message' => 'Currículo carregado com sucesso',
                'data' => $curriculo
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Currículo não encontrado',
                'error' => 'O currículo associado não existe'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar currículo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

// $request->validate([
//     'curriculo' => 'required|file|mimes:pdf|max:2048',
// ]);

// $file = $request->file('curriculo');

// $path = $file->storeAs('curriculos', time() . '_' . $file->getClientOriginalName());
// return response()->json(['message' => 'Currículo enviado com sucesso!', 'path' => $path], 200);
