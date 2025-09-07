<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musica extends Model
{
    use HasFactory;

    protected $table = 'musicas';

    protected $fillable = [
        'titulo',
        'artista',
        'youtube_url',
        'visualizacoes',
        'posicao_top5',
        'status'
    ];

    protected $casts = [
        'visualizacoes' => 'integer',
        'posicao_top5' => 'integer',
    ];


    public function usuario()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovada');
    }


    public function scopeTop5($query)
    {
        return $query->whereNotNull('posicao_top5')
            ->orderBy('posicao_top5', 'asc');
    }


    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }


    public static function atualizarTop5()
    {

        $top5Musicas = self::aprovadas()
            ->orderBy('visualizacoes', 'desc')
            ->limit(5)
            ->get();

        self::whereNotNull('posicao_top5')->update(['posicao_top5' => null]);


        foreach ($top5Musicas as $index => $musica) {
            $musica->update(['posicao_top5' => $index + 1]);
        }

        return $top5Musicas;
    }


    public function atribuirPosicaoTop5(int $posicao): bool
    {

        if ($posicao < 1 || $posicao > 5) {
            return false;
        }


        if ($this->status !== 'aprovada') {
            return false;
        }


        if ($this->posicao_top5) {
            $this->removerDoTop5();
        }


        $musicaNaPosicao = self::where('posicao_top5', $posicao)->first();
        if ($musicaNaPosicao) {
            $musicaNaPosicao->moverParaProximaPosicao();
        }


        $this->update(['posicao_top5' => $posicao]);

        return true;
    }


    public function removerDoTop5(): void
    {
        if ($this->posicao_top5) {
            $posicaoAtual = $this->posicao_top5;


            $this->update(['posicao_top5' => null]);


            self::where('posicao_top5', '>', $posicaoAtual)
                ->decrement('posicao_top5');
        }
    }


    public function moverParaProximaPosicao(): void
    {
        if ($this->posicao_top5) {
            $proximaPosicao = $this->posicao_top5 + 1;


            if ($proximaPosicao > 5) {
                $this->update(['posicao_top5' => null]);
            } else {

                $musicaNaProximaPosicao = self::where('posicao_top5', $proximaPosicao)->first();
                if (!$musicaNaProximaPosicao) {
                    $this->update(['posicao_top5' => $proximaPosicao]);
                } else {

                    $this->update(['posicao_top5' => null]);
                }
            }
        }
    }


    public static function reorganizarTop5(): array
    {
        // Limpar todas as posições atuais primeiro
        self::whereNotNull('posicao_top5')->update(['posicao_top5' => null]);

        // Buscar as 5 músicas aprovadas com mais visualizações
        $top5Musicas = self::aprovadas()
            ->orderBy('visualizacoes', 'desc')
            ->limit(5)
            ->get();

        // Atribuir novas posições
        $resultado = [];
        foreach ($top5Musicas as $index => $musica) {
            $posicao = $index + 1;
            $musica->update(['posicao_top5' => $posicao]);
            $resultado[] = [
                'posicao' => $posicao,
                'musica' => $musica->fresh() // Recarregar para ter os dados atualizados
            ];
        }

        return $resultado;
    }
}
