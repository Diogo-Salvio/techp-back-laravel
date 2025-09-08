<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Musica;

class MusicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $musicas = [
            [
                'titulo' => 'Chico Mineiro',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=exemplo1',
                'visualizacoes' => 1500000,
                'posicao_top5' => 1,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Boi Soberano',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=exemplo2',
                'visualizacoes' => 1200000,
                'posicao_top5' => 2,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Pagode em Brasília',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=exemplo3',
                'visualizacoes' => 1000000,
                'posicao_top5' => 3,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Rei do Gado',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=exemplo4',
                'visualizacoes' => 900000,
                'posicao_top5' => 4,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Fogo de Chão',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=exemplo5',
                'visualizacoes' => 800000,
                'posicao_top5' => 5,
                'status' => 'aprovada'
            ]
        ];

        foreach ($musicas as $musica) {
            Musica::create($musica);
        }
    }
}