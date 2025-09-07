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

        Route::delete('/musicas/{musica}', [MusicaController::class, 'destroy']);
        Route::patch('/musicas/{musica}/posicao', [MusicaController::class, 'updatePosicao']);
        Route::post('/musicas/reorganizar-top5', [MusicaController::class, 'reorganizarTop5']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
