<?php

namespace App\Http\Controllers;

use App\Models\Musica;
use App\Models\SugestaoMusica;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SugestaoMusicaController extends Controller
{
    /**
     * Sugerir nova música
     */
    public function sugerir(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'youtube_url' => 'required|url',
                'video_id' => 'required|string|max:50',
                'comentario_sugestao' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validar se o video_id é válido
            if (!$this->isValidVideoId($request->video_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID do vídeo inválido. Use um ID válido do YouTube.'
                ], 422);
            }


            $youtubeData = $this->extractYouTubeData($request->youtube_url, $request->video_id);

            $sugestao = SugestaoMusica::create([
                'titulo' => $youtubeData['titulo'] ?? 'Título não encontrado', // Extrair título do YouTube
                'artista' => $youtubeData['artista'] ?? 'Não identificado',
                'youtube_url' => $request->youtube_url,
                'visualizacoes' => $youtubeData['visualizacoes'] ?? 0,
                'status' => 'pendente',
                'comentario_sugestao' => $request->comentario_sugestao,
                'user_id' => auth()->id() // Se houver usuário logado
            ]);

            return response()->json([
                'success' => true,
                'data' => $sugestao,
                'message' => 'Música sugerida com sucesso! Aguarde aprovação.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao sugerir música: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar sugestões pendentes (apenas admins)
     */
    public function pendentes(): JsonResponse
    {
        try {
            $pendentes = SugestaoMusica::pendentes()
                ->with('usuario')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pendentes,
                'message' => 'Sugestões pendentes carregadas'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar sugestões: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Aprovar sugestão e mover para tabela de músicas (apenas admins)
     */
    public function aprovar(Request $request, SugestaoMusica $sugestao): JsonResponse
    {
        try {
            // Criar música na tabela principal
            $musica = Musica::create([
                'titulo' => $sugestao->titulo,
                'artista' => $sugestao->artista,
                'letra' => $sugestao->letra,
                'youtube_url' => $sugestao->youtube_url,
                'visualizacoes' => $sugestao->visualizacoes,
                'posicao_top5' => $request->posicao_top5 ?? null,
                'status' => 'aprovada',
                'comentario_sugestao' => $sugestao->comentario_sugestao
            ]);

            // Marcar sugestão como aprovada
            $sugestao->update(['status' => 'aprovada']);

            return response()->json([
                'success' => true,
                'data' => $musica,
                'message' => 'Sugestão aprovada e música criada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar sugestão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reprovar sugestão (apenas admins)
     */
    public function reprovar(Request $request, SugestaoMusica $sugestao): JsonResponse
    {
        try {
            $sugestao->update(['status' => 'reprovada']);

            return response()->json([
                'success' => true,
                'message' => 'Sugestão reprovada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reprovar sugestão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar se é um video_id válido do YouTube
     */
    private function isValidVideoId(string $videoId): bool
    {
        // YouTube video IDs têm 11 caracteres e contêm apenas letras, números, hífens e underscores
        return preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId) === 1;
    }

    /**
     * Extrair dados do YouTube (título, artista, visualizações)
     */
    private function extractYouTubeData(string $url, string $videoId = null): array
    {
        try {
            // Se não tiver videoId, extrair da URL
            if (!$videoId) {
                $videoId = $this->extractVideoIdFromUrl($url);
            }

            if (!$videoId) {
                return [
                    'artista' => 'Tião Carreiro e Pardinho',
                    'visualizacoes' => 0,
                    'video_id' => null
                ];
            }

            // Tentar extrair dados usando web scraping (método mais simples)
            $youtubeData = $this->scrapeYouTubeData($videoId);

            return [
                'titulo' => $youtubeData['titulo'] ?? 'Título não encontrado',
                'artista' => $youtubeData['artista'] ?? 'Tião Carreiro e Pardinho',
                'visualizacoes' => $youtubeData['visualizacoes'] ?? 0,
                'video_id' => $videoId,
                'duracao' => $youtubeData['duracao'] ?? null,
                'thumbnail' => $youtubeData['thumbnail'] ?? null
            ];
        } catch (\Exception $e) {
            // Em caso de erro, retorna dados básicos
            return [
                'titulo' => 'Título não encontrado',
                'artista' => 'Tião Carreiro e Pardinho',
                'visualizacoes' => 0,
                'video_id' => $videoId
            ];
        }
    }

    /**
     * Extrair ID do vídeo da URL do YouTube
     */
    private function extractVideoIdFromUrl(string $url): ?string
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/',
            '/youtube\.com\/watch\?.*v=([^&\n?#]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Fazer scraping dos dados do YouTube
     */
    private function scrapeYouTubeData(string $videoId): array
    {
        try {
            // URL para extrair dados básicos do YouTube
            $url = "https://www.youtube.com/watch?v={$videoId}";

            // Inicializa o cURL
            $ch = curl_init();

            // Configura o cURL para a requisição
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_TIMEOUT => 15
            ]);

            // Faz a requisição
            $response = curl_exec($ch);

            if ($response === false) {
                throw new \Exception("Erro ao acessar o YouTube: " . curl_error($ch));
            }

            curl_close($ch);

            // Extrair dados do HTML
            $data = $this->parseYouTubeHtml($response, $videoId);

            return $data;
        } catch (\Exception $e) {
            // Retorna dados básicos em caso de erro
            return [
                'artista' => 'Tião Carreiro e Pardinho',
                'visualizacoes' => 0
            ];
        }
    }

    /**
     * Parsear HTML do YouTube para extrair dados
     */
    private function parseYouTubeHtml(string $html, string $videoId): array
    {
        $data = [
            'titulo' => 'Título não encontrado',
            'artista' => 'Tião Carreiro e Pardinho',
            'visualizacoes' => 0,
            'duracao' => null,
            'thumbnail' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg"
        ];

        try {
            // Extrair título do YouTube usando a lógica da função original
            if (preg_match('/<title>(.+?) - YouTube<\/title>/', $html, $titleMatches)) {
                $title = html_entity_decode($titleMatches[1], ENT_QUOTES);
                $data['titulo'] = $title;
            } else {
                // Fallback para padrão JSON
                if (preg_match('/"title":"([^"]+)"/', $html, $matches)) {
                    $title = html_entity_decode($matches[1], ENT_QUOTES);
                    $data['titulo'] = $title;
                } else {
                    // Fallback para padrão mais simples
                    if (preg_match('/<title>([^<]+)</', $html, $matches)) {
                        $title = trim($matches[1]);
                        $title = str_replace(' - YouTube', '', $title);
                        $title = html_entity_decode($title, ENT_QUOTES);
                        $data['titulo'] = $title;
                    }
                }
            }

            // Extrair visualizações usando a lógica mais robusta
            // Procura pelo padrão de visualizações no JSON dos dados do vídeo
            if (preg_match('/"viewCount":\s*"(\d+)"/', $html, $viewMatches)) {
                $data['visualizacoes'] = (int)$viewMatches[1];
            } else {
                // Tenta um padrão alternativo
                if (preg_match('/\"viewCount\"\s*:\s*{.*?\"simpleText\"\s*:\s*\"([\d,\.]+)\"/', $html, $viewMatches)) {
                    $data['visualizacoes'] = (int)str_replace(['.', ','], '', $viewMatches[1]);
                } else {
                    // Fallback para formato brasileiro
                    if (preg_match('/(\d+(?:\.\d+)?)\s*(?:mil|milhões?|biliões?)\s*de\s*visualizações/i', $html, $matches)) {
                        $data['visualizacoes'] = $this->parseViewCount($matches[1], $matches[0]);
                    }
                }
            }

            // Extrair canal/artista
            if (preg_match('/"ownerText":\{"runs":\[\{"text":"([^"]+)"\}/', $html, $matches)) {
                $data['artista'] = $matches[1];
            }

            // Extrair duração
            if (preg_match('/"lengthSeconds":"(\d+)"/', $html, $matches)) {
                $data['duracao'] = $this->formatDuration($matches[1]);
            }
        } catch (\Exception $e) {
            // Continua com dados básicos se houver erro no parsing
        }

        return $data;
    }

    /**
     * Converter contagem de visualizações para número
     */
    private function parseViewCount(string $number, string $fullText): int
    {
        $number = (float) str_replace(',', '.', $number);

        if (stripos($fullText, 'milhão') !== false) {
            return (int) ($number * 1000000);
        } elseif (stripos($fullText, 'mil') !== false) {
            return (int) ($number * 1000);
        } elseif (stripos($fullText, 'bilhão') !== false) {
            return (int) ($number * 1000000000);
        }

        return (int) $number;
    }

    /**
     * Formatar duração em segundos para formato legível
     */
    private function formatDuration(string $seconds): string
    {
        $seconds = (int) $seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
    }
}
