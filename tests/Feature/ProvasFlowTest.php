<?php

namespace Tests\Feature;

use App\Modules\Provas\Models\Prova;
use App\Modules\Provas\Models\Questao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvasFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->popularBaseComDemoEnem();
    }

    public function test_simulado_iniciar_responder_finalizar_e_resultado_consultavel(): void
    {
        $provaSimuladoId = (int) Prova::query()
            ->where('tipo', 'simulado')
            ->orderBy('id')
            ->value('id');
        $this->assertGreaterThan(0, $provaSimuladoId);

        $this->postJson(
            "/api/provas/{$provaSimuladoId}/iniciar?expand=prova,questoes&per_page=3",
            [],
            $this->cabecalhosUsuario(),
        )
            ->assertOk()
            ->assertJsonStructure([
                'sessao',
                'prova',
                'questoes' => ['data', 'meta'],
            ]);

        $questao = Questao::query()
            ->where('prova_id', $provaSimuladoId)
            ->orderBy('id')
            ->first();
        $this->assertNotNull($questao);

        $indiceCorreto = self::indicePrimeiraOpcaoCorreta($questao->opcoes);
        $this->assertNotNull($indiceCorreto);

        $this->putJson("/api/provas/{$provaSimuladoId}/questoes/{$questao->id}/resposta", [
            'opcao_id' => $indiceCorreto,
        ], $this->cabecalhosUsuario())
            ->assertOk()
            ->assertJsonPath('feedback.acertou', true)
            ->assertJsonPath('feedback.gabarito_opcao_id', $indiceCorreto);

        $this->postJson("/api/provas/{$provaSimuladoId}/finalizar", [], $this->cabecalhosUsuario())
            ->assertOk();

        $this->getJson("/api/resultados/{$provaSimuladoId}", $this->cabecalhosUsuario())
            ->assertOk()
            ->assertJsonStructure([
                'prova_id',
                'user_id',
                'disciplinas',
                'totais',
            ]);
    }

    public function test_iniciar_sem_query_expand_retorna_payload_legado_somente_prova_id(): void
    {
        $provaSimuladoId = (int) Prova::query()
            ->where('tipo', 'simulado')
            ->orderBy('id')
            ->value('id');

        $json = $this->postJson(
            "/api/provas/{$provaSimuladoId}/iniciar",
            [],
            $this->cabecalhosUsuario(),
        )->assertOk()->json();

        $this->assertArrayHasKey('prova_id', $json);
        $this->assertArrayNotHasKey('sessao', $json);
    }
}
