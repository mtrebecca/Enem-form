<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastro_novo_utilizador_retorna_201_e_mensagem_sucesso(): void
    {
        $this->postJson('/api/auth/register', [
            'nome' => 'Joao',
            'email' => 'joao@enem.dev',
            'senha' => 'Joao1234',
        ])
            ->assertStatus(201)
            ->assertJsonPath('message', 'Usuario cadastrado com sucesso');
    }

    public function test_cadastro_email_duplicado_retorna_422(): void
    {
        $payload = ['nome' => 'A', 'email' => 'dup@enem.dev', 'senha' => 'Senha123'];

        $this->postJson('/api/auth/register', $payload)->assertCreated();
        $this->postJson('/api/auth/register', $payload)
            ->assertStatus(422)
            ->assertJsonPath('message', 'Este email ja esta cadastrado.');
    }

    public function test_cadastro_sem_email_retorna_422(): void
    {
        $this->postJson('/api/auth/register', ['nome' => 'X', 'senha' => 'Senha123'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Dados invalidos.')
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_login_credenciais_validas_retorna_token_e_utilizador(): void
    {
        $this->popularBaseComDemoEnem();

        $this->postJson('/api/auth/login', [
            'email' => self::USUARIO_DEMO_EMAIL,
            'senha' => self::USUARIO_DEMO_SENHA,
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Login realizado com sucesso')
            ->assertJsonStructure(['token', 'user' => ['id', 'nome', 'email']]);
    }

    public function test_login_senha_errada_retorna_401(): void
    {
        $this->popularBaseComDemoEnem();

        $this->postJson('/api/auth/login', [
            'email' => self::USUARIO_DEMO_EMAIL,
            'senha' => 'errada',
        ])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Credenciais invalidas');
    }

    public function test_esqueci_senha_email_cadastrado_retorna_200(): void
    {
        $this->popularBaseComDemoEnem();

        $this->postJson('/api/auth/esqueci-senha', ['email' => self::USUARIO_DEMO_EMAIL])
            ->assertOk()
            ->assertJsonPath('sent', true);
    }

    public function test_esqueci_senha_email_desconhecido_retorna_404(): void
    {
        $this->postJson('/api/auth/esqueci-senha', ['email' => 'ninguem@enem.dev'])
            ->assertNotFound()
            ->assertJsonPath('sent', false);
    }

    public function test_logout_retorna_200_e_mensagem(): void
    {
        $this->popularBaseComDemoEnem();

        $token = $this->postJson('/api/auth/login', [
            'email' => self::USUARIO_DEMO_EMAIL,
            'senha' => self::USUARIO_DEMO_SENHA,
        ])->assertOk()->json('token');

        $this->postJson('/api/auth/logout', [], ['Authorization' => "Bearer {$token}"])
            ->assertOk()
            ->assertJsonPath('message', 'Logout realizado com sucesso');
    }
}
