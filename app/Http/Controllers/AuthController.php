<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nome' => ['required'],
            'email' => ['required', 'email', 'unique:usuarios,email'],
            'senha' => ['required', 'min:5'],
            'tipo' => ['in:estudante,empresa,admin'],
        ]);

        $user = Usuario::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'senha' => bcrypt($data['senha']),
            'tipo' => $data['tipo'],
        ]);

        if ($user->tipo === 'estudante') {
            \App\Models\Estudante::create([
                'usuario_id' => $user->id,
            ]);
        } elseif ($user->tipo === 'empresa') {
            \App\Models\Empresa::create([
                'usuario_id' => $user->id,
            ]);
        }

        return response()->json($user);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required'],
        ]);

        $user = Usuario::where('email', $data['email'])->first();

        if (!$user || !password_verify($data['senha'], $user->senha)) {
            return response()->json('Credenciais inválidas.', 422);
        }

        if ($user->tipo === 'admin') {
            $abilities = ['admin', 'empresa', 'estudante'];
        } else {
            $abilities = $user->tipo === 'empresa' ? ['empresa'] : ['estudante'];
        }
        $token = $user->createToken('api', $abilities)->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function loginWithFirebase(Request $request): JsonResponse
    {


        $data = $request->validate([
            'firebase_uid' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $nome = explode('@', $data['email'], 2)[0];

        $randomPassword = bin2hex(random_bytes(16));
        $hashedPassword = bcrypt($randomPassword);

        $user = Usuario::updateOrCreate(
            ['firebase_uid' => $data['firebase_uid']],
            [
                'email' => $data['email'],
                'nome' => $nome,
                'senha' => $hashedPassword,
                'tipo' => 'estudante',
            ]
        );

        $user->load('estudante');

        if (!$user->estudante) {
            \App\Models\Estudante::create([
                'usuario_id' => $user->id,
            ]);
        }


        $plainToken = Str::random(40);
        $hashedToken = hash('sha256', $plainToken);

        $user->tokens()->create([
            'name' => 'firebase-login',
            'token' => $hashedToken,
            'abilities' => ['estudante'],
        ]);

        return response()->json([
            'user' => $user,
            'access_token' => $plainToken,
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $table = config('sanctum.storage.database.table', 'personal_access_tokens');

        DB::table($table)
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', get_class($user))
            ->delete();

        return response()->json('Logout realizado com sucesso.', 202);
    }
}
