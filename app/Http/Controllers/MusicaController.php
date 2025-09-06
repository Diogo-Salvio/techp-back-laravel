<?php

namespace App\Http\Controllers;

use App\Models\Musica;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MusicaController extends Controller
{
    /**
     * Listar todas as músicas aprovadas
     */
    public function index(): JsonResponse
    {
        try {
            $musicas = Musica::aprovadas()
                ->orderBy('posicao_top5', 'asc')
                ->orderBy('visualizacoes', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $musicas,
                'message' => 'Músicas carregadas com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar músicas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar top 5 músicas
     */
    public function top5(): JsonResponse
    {
        try {
            $top5 = Musica::top5()->get();

            return response()->json([
                'success' => true,
                'data' => $top5,
                'message' => 'Top 5 carregado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar top 5: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprovar música (apenas usuários autenticados)
     */
    public function aprovar(Request $request, Musica $musica): JsonResponse
    {
        try {
            // Verificar se usuário está autenticado
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $musica->update([
                'status' => 'aprovada',
                'posicao_top5' => $request->posicao_top5 ?? null
            ]);

            return response()->json([
                'success' => true,
                'data' => $musica,
                'message' => 'Música aprovada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar música: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reprovar música (apenas usuários autenticados)
     */
    public function reprovar(Request $request, Musica $musica): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $musica->update(['status' => 'reprovada']);

            return response()->json([
                'success' => true,
                'message' => 'Música reprovada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reprovar música: ' . $e->getMessage()
            ], 500);
        }
    }
}
