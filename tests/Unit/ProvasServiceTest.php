<?php

namespace Tests\Unit;

use App\Modules\Provas\Services\ProvasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvasServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->popularBaseComDemoEnem();
    }

    public function test_listar_retorna_colecao_com_titulo_e_disciplinas(): void
    {
        $provas = $this->app->make(ProvasService::class)->listar();

        $this->assertNotEmpty($provas);
        $this->assertArrayHasKey('titulo', $provas[0]);
        $this->assertArrayHasKey('disciplinas', $provas[0]);
    }
}
