<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_middleware_allows_admin_users()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'email_verified_at' => now()
        ]);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        // Simular autenticação
        auth()->setUser($admin);

        $middleware = new AdminMiddleware();
        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function admin_middleware_blocks_non_admin_users()
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'email_verified_at' => now()
        ]);

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Simular autenticação
        auth()->setUser($user);

        $middleware = new AdminMiddleware();
        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(403, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('administradores', $responseData['message']);
    }

    /** @test */
    public function admin_middleware_blocks_unauthenticated_users()
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(function () {
            return null;
        });

        $middleware = new AdminMiddleware();
        $response = $middleware->handle($request, function ($req) {
            return response()->json(['success' => true]);
        });

        $this->assertEquals(401, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContainsString('não autenticado', $responseData['message']);
    }
}
