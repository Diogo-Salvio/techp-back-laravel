<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicaController;
use App\Http\Controllers\SugestaoMusicaController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rota de boas-vindas da API
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'As Melhores de Tião Carreiro e Pardinho!',
        'version' => '1.0.0',
        'endpoints' => [
            'GET /api/musicas' => 'Listar todas as músicas',
            'GET /api/musicas/top5' => 'Listar Top 5 músicas',
            'POST /api/sugestoes' => 'Sugerir nova música',
            'POST /api/login' => 'Fazer login',
            'GET /api/sugestoes/pendentes' => 'Ver sugestões pendentes (Admin)',
            'PATCH /api/sugestoes/{id}/aprovar' => 'Aprovar sugestão (Admin)',
            'PATCH /api/sugestoes/{id}/reprovar' => 'Reprovar sugestão (Admin)',
            'PATCH /api/musicas/{id}' => 'Editar música (Admin)',
            'DELETE /api/musicas/{id}' => 'Remover música (Admin)',
            'PATCH /api/musicas/{id}/posicao' => 'Atualizar posição (Admin)',
            'POST /api/musicas/reorganizar-top5' => 'Reorganizar Top 5 (Admin)'
        ]
    ]);
});

Route::get('/musicas', [MusicaController::class, 'index']);
Route::get('/musicas/top5', [MusicaController::class, 'top5']);

Route::post('/sugestoes', [SugestaoMusicaController::class, 'sugerir']);

// autenticação
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // admin
    Route::middleware('admin')->group(function () {

        Route::get('/sugestoes/pendentes', [SugestaoMusicaController::class, 'pendentes']);
        Route::patch('/sugestoes/{sugestao}/aprovar', [SugestaoMusicaController::class, 'aprovar']);
        Route::patch('/sugestoes/{sugestao}/reprovar', [SugestaoMusicaController::class, 'reprovar']);

        Route::patch('/musicas/{musica}', [MusicaController::class, 'update']);
        Route::delete('/musicas/{musica}', [MusicaController::class, 'destroy']);
        Route::patch('/musicas/{musica}/posicao', [MusicaController::class, 'updatePosicao']);
        Route::post('/musicas/reorganizar-top5', [MusicaController::class, 'reorganizarTop5']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
