<?php

namespace Tests\Feature;

use App\Models\Musica;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MusicaTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $adminToken;

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
    }

    /** @test */
    public function can_list_all_musicas()
    {
        // Criar algumas músicas de teste
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

        $response = $this->getJson('/api/musicas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'titulo', 'artista', 'visualizacoes', 'status']
                ],
                'message'
            ])
            ->assertJson(['success' => true]);

        // Verificar se está ordenado por visualizações (desc)
        $data = $response->json('data');
        $this->assertEquals(200000, $data[0]['visualizacoes']);
        $this->assertEquals(100000, $data[1]['visualizacoes']);
    }

    /** @test */
    public function can_get_top5_musicas()
    {
        // Criar músicas com posições no top 5
        Musica::create([
            'titulo' => 'Música 1',
            'artista' => 'Artista 1',
            'visualizacoes' => 100000,
            'posicao_top5' => 1,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        Musica::create([
            'titulo' => 'Música 2',
            'artista' => 'Artista 2',
            'visualizacoes' => 200000,
            'posicao_top5' => 2,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=2'
        ]);

        $response = $this->getJson('/api/musicas/top5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'titulo', 'artista', 'posicao_top5']
                ],
                'message'
            ])
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['posicao_top5']);
        $this->assertEquals(2, $data[1]['posicao_top5']);
    }

    /** @test */
    public function admin_can_delete_musica()
    {
        $musica = Musica::create([
            'titulo' => 'Música para deletar',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->deleteJson("/api/musicas/{$musica->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('musicas', ['id' => $musica->id]);
    }

    /** @test */
    public function admin_can_update_musica_position()
    {
        $musica = Musica::create([
            'titulo' => 'Música para posicionar',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->patchJson("/api/musicas/{$musica->id}/posicao", [
            'posicao_top5' => 3
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $musica->refresh();
        $this->assertEquals(3, $musica->posicao_top5);
    }

    /** @test */
    public function admin_can_reorganize_top5()
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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/musicas/reorganizar-top5');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verificar se as posições foram atribuídas corretamente
        $musica1 = Musica::where('titulo', 'Música 2')->first();
        $musica2 = Musica::where('titulo', 'Música 1')->first();

        $this->assertEquals(1, $musica1->posicao_top5);
        $this->assertEquals(2, $musica2->posicao_top5);
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $user = User::create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $musica = Musica::create([
            'titulo' => 'Música',
            'artista' => 'Artista',
            'visualizacoes' => 100000,
            'status' => 'aprovada',
            'youtube_url' => 'https://youtube.com/watch?v=1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/musicas/{$musica->id}");

        $response->assertStatus(403);
    }
}
