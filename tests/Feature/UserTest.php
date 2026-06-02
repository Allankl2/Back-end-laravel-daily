<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_autenticado_pode_listar_usuarios(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_listar_usuarios_sem_autenticacao_retorna_401(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_retorna_estrutura_correta(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/users');

        $response->assertJsonStructure([
            'users' => [
                '*' => ['id', 'name', 'email', 'created_at'],
            ],
        ]);
    }

    public function test_retorna_todos_os_usuarios(): void
    {
        User::factory(5)->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson('/api/users');

        $response->assertJsonCount(6, 'users');
    }
}
