<?php

namespace App\Http\Controllers;

use App\Models\Musica;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MusicaController extends Controller
{
    /**
     * Listar todas as músicas ordenadas por visualizações
     */
    public function index(): JsonResponse
    {
        try {
            $musicas = Musica::orderBy('visualizacoes', 'desc')->get();

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
     * Listar top 5 músicas
     */
    public function top5(): JsonResponse
    {
        try {
            $top5 = Musica::whereNotNull('posicao_top5')
                ->orderBy('posicao_top5', 'asc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $top5,
                'message' => 'Top 5 músicas carregadas com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar top 5: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Musica $musica): JsonResponse
    {
        try {

            $estavaNoTop5 = $musica->posicao_top5 !== null;

            $musica->delete();


            if ($estavaNoTop5) {
                Musica::reorganizarTop5();
            }

            return response()->json([
                'success' => true,
                'message' => 'Música removida com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover música: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updatePosicao(Request $request, Musica $musica): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'posicao_top5' => 'required|integer|min:1|max:5'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }


            if ($musica->status !== 'aprovada') {
                return response()->json([
                    'success' => false,
                    'message' => 'Apenas músicas aprovadas podem ter posição no top 5'
                ], 422);
            }


            $sucesso = $musica->atribuirPosicaoTop5($request->posicao_top5);

            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atribuir posição no top 5'
                ], 422);
            }


            $musica->refresh();

            return response()->json([
                'success' => true,
                'data' => $musica,
                'message' => 'Posição atualizada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar posição: ' . $e->getMessage()
            ], 500);
        }
    }


    public function reorganizarTop5(): JsonResponse
    {
        try {
            $resultado = Musica::reorganizarTop5();

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'Top 5 reorganizado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reorganizar top 5: ' . $e->getMessage()
            ], 500);
        }
    }
}
