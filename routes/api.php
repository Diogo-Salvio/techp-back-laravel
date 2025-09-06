<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicaController;
use App\Http\Controllers\SugestaoMusicaController;

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

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Gerenciar sugestões
    Route::get('/sugestoes/pendentes', [SugestaoMusicaController::class, 'pendentes']);
    Route::patch('/sugestoes/{sugestao}/aprovar', [SugestaoMusicaController::class, 'aprovar']);
    Route::patch('/sugestoes/{sugestao}/reprovar', [SugestaoMusicaController::class, 'reprovar']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
