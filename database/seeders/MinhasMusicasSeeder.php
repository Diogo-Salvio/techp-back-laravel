<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Musica;

class MinhasMusicasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $musicas = [
            [
                'titulo' => 'Música 1 - YouTube',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=N8eh51GW-aY',
                'visualizacoes' => 0,
                'posicao_top5' => null,
                'status' => 'aprovada',
                'letra' => null,
                'comentario_sugestao' => 'Adicionada via seeder - Link 1'
            ],
            [
                'titulo' => 'Música 2 - YouTube',
                'artista' => 'Tião Carreiro e Pardinho',
                'youtube_url' => 'https://www.youtube.com/watch?v=3ZFO_0PFuHI',
                'visualizacoes' => 0,
                'posicao_top5' => null,
                'status' => 'aprovada',
                'letra' => null,
                'comentario_sugestao' => 'Adicionada via seeder - Link 2'
            ]
        ];

        foreach ($musicas as $musica) {
            Musica::create($musica);
        }
    }
}
