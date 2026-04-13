<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->popularBaseComDemoEnem();
    }

    public function test_dashboard_retorna_provas_e_historico(): void
    {
        $resposta = $this->getJson('/api/dashboard', $this->cabecalhosUsuario())->assertOk();

        $resposta->assertJsonStructure([
            'provas',
            'historico',
        ]);
        $this->assertNotEmpty($resposta->json('provas'));
    }
}
