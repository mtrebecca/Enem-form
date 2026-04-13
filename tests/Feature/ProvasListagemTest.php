<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvasListagemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->popularBaseComDemoEnem();
    }

    public function test_listagem_retorna_array_nao_vazio_com_titulo_e_disciplinas(): void
    {
        $lista = $this->getJson('/api/provas')->assertOk()->json();

        $this->assertIsArray($lista);
        $this->assertNotEmpty($lista);
        $this->assertArrayHasKey('titulo', $lista[0]);
        $this->assertArrayHasKey('disciplinas', $lista[0]);
    }

    public function test_detalhe_prova_id_inexistente_retorna_404(): void
    {
        $this->getJson('/api/provas/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Prova nao encontrada');
    }
}
