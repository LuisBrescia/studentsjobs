<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\ChatAPIController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CandidaturaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// * AuthController
Route::get('initialize-sessions-with-last-message/{id}', [ChatSessionController::class, 'initializeSessionsWithLastMessage']);

Route::prefix('auth')->group(function () {
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('login/firebase', [\App\Http\Controllers\AuthController::class, 'loginWithFirebase']); // Firebase
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', [\App\Http\Controllers\AuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    });

    Route::prefix('empresas')->middleware([\App\Http\Middleware\EmpresaValida::class])->group(function () {
        Route::get('/vagas', [\App\Http\Controllers\VagaController::class, 'vagasDaEmpresaAutenticada']);
        Route::get('/candidaturas', [\App\Http\Controllers\CandidaturaController::class, 'getCandidaturasEmpresaAutenticada']);
        Route::get('/', [\App\Http\Controllers\EmpresaController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\EmpresaController::class, 'show']);
    });

    Route::prefix('estudantes')->middleware([\App\Http\Middleware\EstudanteValido::class])->group(function () {
        Route::get('/curriculo', [\App\Http\Controllers\EstudanteController::class, 'carregarCurriculo']);
        Route::post('/curriculo', [\App\Http\Controllers\EstudanteController::class, 'salvarCurriculo']);

        Route::get('/', [\App\Http\Controllers\EstudanteController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\EstudanteController::class, 'show']);
    });

    Route::prefix('vagas')->group(function () {
        Route::get('/', [\App\Http\Controllers\VagaController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\VagaController::class, 'store']);
    });

    Route::prefix('candidaturas')->middleware([\App\Http\Middleware\EstudanteValido::class])->group(function () {
        Route::get('/', [CandidaturaController::class, 'index']);
        Route::post('/', [CandidaturaController::class, 'store']);
        Route::get('/{id}', [CandidaturaController::class, 'show']);
        Route::put('/{id}', [CandidaturaController::class, 'update']);
        Route::delete('/{id}', [CandidaturaController::class, 'destroy']);
    });

    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatSessionController::class, 'index']);
        Route::post('/', [ChatSessionController::class, 'store']);
        Route::delete('/{id}', [ChatSessionController::class, 'destroy']);
    });

    Route::prefix('chat-api')->group(function () {
        Route::get('/', [ChatAPIController::class, 'getUserSessionsWithLastMessages']);
        // Route::get('/{id}', [ChatAPIController::class, 'getMessagesForSession']);
    });

    Route::prefix('mensagem')->group(function () {
        Route::get('/{sessionId}/historico', [MessageController::class, 'mountHistoryBySessionId']);
        Route::post('/', [MessageController::class, 'sendMessage']);
    });
});

Route::get('debug', fn() => response()->json(['status' => 'online']));
