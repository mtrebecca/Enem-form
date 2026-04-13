<?php

namespace Tests\Feature;

use App\Modules\Provas\Models\Questao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreinoEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->popularBaseComDemoEnem();
    }

    public function test_disciplinas_e_questao_aleatoria_retornam_estrutura_esperada(): void
    {
        $disciplinas = $this->getJson('/api/treino/disciplinas')->assertOk()->json();
        $this->assertIsArray($disciplinas);
        $this->assertNotEmpty($disciplinas);

        $this->getJson('/api/treino/questao-aleatoria')
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'disciplina',
                'enunciado',
                'opcoes',
            ]);
    }

    public function test_responder_com_gabarito_correto_retorna_acertou_true(): void
    {
        $payload = $this->getJson('/api/treino/questao-aleatoria')->assertOk()->json();
        $questao = Questao::query()->findOrFail($payload['id']);

        $indiceCorreto = self::indicePrimeiraOpcaoCorreta($questao->opcoes);
        $this->assertNotNull($indiceCorreto);

        $this->postJson('/api/treino/responder', [
            'questao_id' => $questao->id,
            'opcao_id' => $indiceCorreto,
        ], $this->cabecalhosUsuario())
            ->assertOk()
            ->assertJsonPath('feedback.acertou', true);
    }
}

