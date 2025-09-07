<?php

namespace Tests\Feature;

use App\Models\Musica;
use App\Models\SugestaoMusica;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SugestaoMusicaTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $adminToken;
    protected $user;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar usuário admin
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'email_verified_at' => now()
        ]);

        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;

        // Criar usuário comum
        $this->user = User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        $this->userToken = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function can_suggest_music()
    {
        // Testar criação direta de sugestão (sem scraping do YouTube)
        $sugestao = SugestaoMusica::create([
            'titulo' => 'Título Teste',
            'artista' => 'Artista Teste',
            'youtube_url' => 'https://youtube.com/watch?v=test123',
            'visualizacoes' => 100000,
            'status' => 'pendente'
        ]);

        $this->assertDatabaseHas('sugestoes_musicas', [
            'titulo' => 'Título Teste',
            'artista' => 'Artista Teste',
            'youtube_url' => 'https://youtube.com/watch?v=test123',
            'status' => 'pendente'
        ]);

        $this->assertEquals('Título Teste', $sugestao->titulo);
        $this->assertEquals('Artista Teste', $sugestao->artista);
        $this->assertEquals('pendente', $sugestao->status);
    }

    /** @test */
    public function cannot_suggest_music_with_invalid_data()
    {
        $response = $this->postJson('/api/sugestoes', [
            'youtube_url' => 'invalid-url',
            'video_id' => 'test123'
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /** @test */
    public function admin_can_list_pending_suggestions()
    {
        // Criar sugestões pendentes
        SugestaoMusica::create([
            'titulo' => 'Sugestão 1',
            'artista' => 'Artista 1',
            'youtube_url' => 'https://youtube.com/watch?v=1',
            'visualizacoes' => 100000,
            'status' => 'pendente'
        ]);

        SugestaoMusica::create([
            'titulo' => 'Sugestão 2',
            'artista' => 'Artista 2',
            'youtube_url' => 'https://youtube.com/watch?v=2',
            'visualizacoes' => 200000,
            'status' => 'pendente'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/sugestoes/pendentes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'titulo', 'artista', 'status']
                ],
                'message'
            ])
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
    }

    /** @test */
    public function admin_can_approve_suggestion()
    {
        $sugestao = SugestaoMusica::create([
            'titulo' => 'Sugestão para aprovar',
            'artista' => 'Artista',
            'youtube_url' => 'https://youtube.com/watch?v=1',
            'visualizacoes' => 100000,
            'status' => 'pendente'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->patchJson("/api/sugestoes/{$sugestao->id}/aprovar");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verificar se a música foi criada
        $this->assertDatabaseHas('musicas', [
            'titulo' => 'Sugestão para aprovar',
            'status' => 'aprovada'
        ]);

        // Verificar se a sugestão foi marcada como aprovada
        $sugestao->refresh();
        $this->assertEquals('aprovada', $sugestao->status);
    }

    /** @test */
    public function admin_can_reject_suggestion()
    {
        $sugestao = SugestaoMusica::create([
            'titulo' => 'Sugestão para rejeitar',
            'artista' => 'Artista',
            'youtube_url' => 'https://youtube.com/watch?v=1',
            'visualizacoes' => 100000,
            'status' => 'pendente'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->patchJson("/api/sugestoes/{$sugestao->id}/reprovar");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verificar se a sugestão foi marcada como reprovada
        $sugestao->refresh();
        $this->assertEquals('reprovada', $sugestao->status);

        // Verificar se nenhuma música foi criada
        $this->assertDatabaseMissing('musicas', [
            'titulo' => 'Sugestão para rejeitar'
        ]);
    }

    /** @test */
    public function approving_suggestion_automatically_reorganizes_top5()
    {
        // Criar algumas músicas existentes
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

        // Criar sugestão com mais visualizações
        $sugestao = SugestaoMusica::create([
            'titulo' => 'Sugestão Top',
            'artista' => 'Artista Top',
            'youtube_url' => 'https://youtube.com/watch?v=top',
            'visualizacoes' => 300000,
            'status' => 'pendente'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->patchJson("/api/sugestoes/{$sugestao->id}/aprovar");

        $response->assertStatus(200);

        // Verificar se a nova música está na posição 1 do top 5
        $topMusica = Musica::where('titulo', 'Sugestão Top')->first();
        $this->assertEquals(1, $topMusica->posicao_top5);
    }

    /** @test */
    public function non_admin_cannot_access_admin_suggestion_routes()
    {
        $sugestao = SugestaoMusica::create([
            'titulo' => 'Sugestão',
            'artista' => 'Artista',
            'youtube_url' => 'https://youtube.com/watch?v=1',
            'visualizacoes' => 100000,
            'status' => 'pendente'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken
        ])->getJson('/api/sugestoes/pendentes');

        $response->assertStatus(403);
    }
}
