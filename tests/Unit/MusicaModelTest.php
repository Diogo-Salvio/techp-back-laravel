<?php

namespace Tests\Unit;

use App\Models\Musica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MusicaModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_musica()
    {
        $musica = Musica::create([
            'titulo' => 'Teste Música',
            'artista' => 'Teste Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=test'
        ]);

        $this->assertDatabaseHas('musicas', [
            'titulo' => 'Teste Música',
            'artista' => 'Teste Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada'
        ]);
    }

    /** @test */
    public function can_reorganize_top5()
    {
        // Criar músicas com visualizações diferentes
        Musica::create([
            'titulo' => 'Música 1',
            'artista' => 'Artista 1',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        Musica::create([
            'titulo' => 'Música 2',
            'artista' => 'Artista 2',
            'visualizacoes' => 200000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=2'
        ]);

        Musica::create([
            'titulo' => 'Música 3',
            'artista' => 'Artista 3',
            'visualizacoes' => 150000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=3'
        ]);

        $resultado = Musica::reorganizarTop5();

        $this->assertCount(3, $resultado);

        // Verificar se as posições foram atribuídas corretamente
        $musica1 = Musica::where('titulo', 'Música 2')->first(); // 200k views
        $musica2 = Musica::where('titulo', 'Música 3')->first(); // 150k views
        $musica3 = Musica::where('titulo', 'Música 1')->first(); // 100k views

        $this->assertEquals(1, $musica1->posicao_top5);
        $this->assertEquals(2, $musica2->posicao_top5);
        $this->assertEquals(3, $musica3->posicao_top5);
    }

    /** @test */
    public function can_assign_specific_position()
    {
        $musica = Musica::create([
            'titulo' => 'Teste Música',
            'artista' => 'Teste Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=test'
        ]);

        $resultado = $musica->atribuirPosicaoTop5(3);

        $this->assertTrue($resultado);
        $musica->refresh();
        $this->assertEquals(3, $musica->posicao_top5);
    }

    /** @test */
    public function cannot_assign_invalid_position()
    {
        $musica = Musica::create([
            'titulo' => 'Teste Música',
            'artista' => 'Teste Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=test'
        ]);

        $resultado = $musica->atribuirPosicaoTop5(6); // Posição inválida

        $this->assertFalse($resultado);
    }

    /** @test */
    public function can_remove_from_top5()
    {
        $musica = Musica::create([
            'titulo' => 'Teste Música',
            'artista' => 'Teste Artista',
            'visualizacoes' => 100000,
            'posicao_top5' => 2,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=test'
        ]);

        $musica->removerDoTop5();

        $musica->refresh();
        $this->assertNull($musica->posicao_top5);
    }

    /** @test */
    public function scope_aprovadas_works()
    {
        Musica::create([
            'titulo' => 'Música Aprovada',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        Musica::create([
            'titulo' => 'Música Pendente',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'status' => 'pendente',
            'youtube_url' => 'https://youtube.com/watch?v=2'
        ]);

        $aprovadas = Musica::aprovadas()->get();

        $this->assertCount(1, $aprovadas);
        $this->assertEquals('Música Aprovada', $aprovadas->first()->titulo);
    }

    /** @test */
    public function scope_top5_works()
    {
        Musica::create([
            'titulo' => 'Música 1',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'posicao_top5' => 1,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        Musica::create([
            'titulo' => 'Música 2',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'posicao_top5' => 2,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=2'
        ]);

        Musica::create([
            'titulo' => 'Música Fora',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'posicao_top5' => null,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=3'
        ]);

        $top5 = Musica::top5()->get();

        $this->assertCount(2, $top5);
        $this->assertEquals(1, $top5->first()->posicao_top5);
        $this->assertEquals(2, $top5->last()->posicao_top5);
    }
}
