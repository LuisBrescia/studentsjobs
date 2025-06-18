<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSanctum extends Middleware
{
    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        // Para requisições API (que esperam JSON)
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Não autenticado. Token inválido ou não fornecido.',
                'error_code' => 'unauthenticated'
            ], Response::HTTP_UNAUTHORIZED));
        }

        // Apenas para rotas web (se aplicável)
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * (Apenas para rotas web)
     */
    protected function redirectTo(Request $request): ?string
    {
        // Retorna null para forçar o uso do unauthenticated() acima
        return null;
    }
}