<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // REGISTER
    // -------------------------------------------------------------------------

    public function test_usuario_pode_se_registrar(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Allan',
            'email'                 => 'allan@teste.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Código OTP enviado para seu email. Verifique sua caixa de entrada.']);

        $this->assertDatabaseHas('users', ['email' => 'allan@teste.com']);
    }

    public function test_registro_falha_com_email_duplicado(): void
    {
        User::factory()->create([
            'email'             => 'allan@teste.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Allan',
            'email'                 => 'allan@teste.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['message' => 'Este email já está cadastrado.']);
    }

    public function test_registro_falha_sem_campos_obrigatorios(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registro_falha_com_senha_curta(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Allan',
            'email'                 => 'allan@teste.com',
            'password'              => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    public function test_registro_falha_sem_confirmacao_de_senha(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'     => 'Allan',
            'email'    => 'allan@teste.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------

    public function test_usuario_pode_fazer_login(): void
    {
        User::factory()->create([
            'email'    => 'allan@teste.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'allan@teste.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user'  => ['id', 'name', 'email'],
                     'token',
                 ]);
    }

    public function test_login_falha_com_senha_errada(): void
    {
        User::factory()->create(['email' => 'allan@teste.com']);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'allan@teste.com',
            'password' => 'senha_errada',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciais inválidas.']);
    }

    public function test_login_falha_com_email_inexistente(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'naoexiste@teste.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciais inválidas.']);
    }

    public function test_login_revoga_token_antigo(): void
    {
        $user     = User::factory()->create(['password' => 'password123']);
        $oldToken = $user->createToken('auth_token')->plainTextToken;

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $this->withHeader('Authorization', "Bearer $oldToken")
             ->getJson('/api/auth/can-access')
             ->assertStatus(401);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_token_expira_apos_um_dia(): void
    {
        $user  = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $token = $response->json('token');

        Carbon::setTestNow(now()->addDay()->addSecond());

        $this->withHeader('Authorization', "Bearer $token")
             ->getJson('/api/auth/can-access')
             ->assertStatus(401);

        Carbon::setTestNow();
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------

    public function test_usuario_pode_fazer_logout(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_falha_sem_token(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // CAN-ACCESS
    // -------------------------------------------------------------------------

    public function test_usuario_autenticado_pode_acessar_can_access(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/auth/can-access');

        $response->assertStatus(200)
                 ->assertJson(['authenticated' => true]);
    }

    public function test_can_access_falha_sem_token(): void
    {
        $response = $this->getJson('/api/auth/can-access');

        $response->assertStatus(401);
    }
}
