<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas
Route::get('/musicas', [MusicaController::class, 'index']);
Route::get('/musicas/top5', [MusicaController::class, 'top5']);
Route::post('/musicas/sugerir', [MusicaController::class, 'sugerir']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/musicas/pendentes', [MusicaController::class, 'pendentes']);
    Route::patch('/musicas/{musica}/aprovar', [MusicaController::class, 'aprovar']);
    Route::patch('/musicas/{musica}/reprovar', [MusicaController::class, 'reprovar']);

    // Rotas para gerenciar sugestões
    Route::patch('/sugestoes/{sugestao}/aprovar', [MusicaController::class, 'aprovarSugestao']);
    Route::patch('/sugestoes/{sugestao}/reprovar', [MusicaController::class, 'reprovarSugestao']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
