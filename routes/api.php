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

// Rotas públicas
Route::get('/musicas', [MusicaController::class, 'index']);
Route::get('/musicas/top5', [MusicaController::class, 'top5']);

// Rotas de sugestões (públicas)
Route::post('/sugestoes', [SugestaoMusicaController::class, 'sugerir']);

// Rotas de autenticação
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Logout e dados do usuário
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Rotas de admin (requerem autenticação + admin)
    Route::middleware('admin')->group(function () {
        // Gerenciar sugestões
        Route::get('/sugestoes/pendentes', [SugestaoMusicaController::class, 'pendentes']);
        Route::patch('/sugestoes/{sugestao}/aprovar', [SugestaoMusicaController::class, 'aprovar']);
        Route::patch('/sugestoes/{sugestao}/reprovar', [SugestaoMusicaController::class, 'reprovar']);

        // Gerenciar músicas
        Route::delete('/musicas/{musica}', [MusicaController::class, 'destroy']);
        Route::patch('/musicas/{musica}/posicao', [MusicaController::class, 'updatePosicao']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
