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
                'youtube_url' => 'https://www.youtube.com/watch?v=lpGGNA6_920',
                'visualizacoes' => 2500000,
                'posicao_top5' => 1,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Boi Soberano',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=3ZFO_0PFuHI',
                'visualizacoes' => 2200000,
                'posicao_top5' => 2,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Pagode em Brasília',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=7ODUHvbqcNs',
                'visualizacoes' => 1800000,
                'posicao_top5' => 3,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Rei do Gado',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=dmzWLsOWMxg',
                'visualizacoes' => 1600000,
                'posicao_top5' => 4,
                'status' => 'aprovada'
            ],
            [
                'titulo' => 'Fogo de Chão',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=foUUT4AKkz0',
                'visualizacoes' => 1400000,
                'posicao_top5' => 5,
                'status' => 'aprovada'
            ]
        ];

        foreach ($musicas as $musica) {
            Musica::create($musica);
        }
    }
}
