<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiUserHeaderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->popularBaseComDemoEnem();
    }

    public function test_dashboard_sem_x_user_id_retorna_401(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }

    public function test_minha_conta_com_cabecalho_retorna_dados_do_utilizador_demo(): void
    {
        $this->getJson('/api/minha-conta', $this->cabecalhosUsuario())
            ->assertOk()
            ->assertJsonPath('email', self::USUARIO_DEMO_EMAIL)
            ->assertJsonStructure(['id', 'nome', 'email']);
    }

    public function test_resultados_prova_inexistente_retorna_404(): void
    {
        $this->getJson('/api/resultados/999999', $this->cabecalhosUsuario())
            ->assertNotFound()
            ->assertJsonPath('message', 'Resultado nao encontrado para esta prova');
    }
}
