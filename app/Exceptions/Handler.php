<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * Lista de inputs que nunca são enviados para a sessão em exceções de validação.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra os callbacks de tratamento de exceções.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Aqui você pode adicionar lógica para reportar erros
            // Ex: enviar para Bugsnag, Sentry, etc.
        });
    }

    /**
     * Converte uma exceção de autenticação em uma resposta JSON.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Não autenticado. Por favor, faça login.',
            'error' => 'Unauthenticated',
            'redirect' => config('app.debug') ? 'auth/login' : null
        ], 401);
    }

    /**
     * Renderiza uma exceção em uma resposta HTTP.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse
     */
    public function render($request, Throwable $e)
    {
        // Para requisições API (que esperam JSON)
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Trata exceções para API.
     */
    protected function handleApiException($request, Throwable $exception): JsonResponse
    {
        $statusCode = $this->getStatusCode($exception);
        $response = [
            'success' => false,
            'message' => $this->getErrorMessage($exception),
            'error' => class_basename($exception)
        ];

        if (config('app.debug')) {
            $response['trace'] = $exception->getTrace();
            $response['code'] = $exception->getCode();
        }

        return response()->json($response, $statusCode);
    }

    protected function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return $exception instanceof AuthenticationException ? 401 : 500;
    }

    protected function getErrorMessage(Throwable $exception): string
    {
        switch (true) {
            case $exception instanceof AuthenticationException:
                return 'Acesso não autorizado';
            case $exception instanceof ModelNotFoundException:
                return 'Recurso não encontrado';
            case $exception instanceof ValidationException:
                return 'Dados inválidos';
            default:
                return 'Erro interno do servidor';
        }
    }
}