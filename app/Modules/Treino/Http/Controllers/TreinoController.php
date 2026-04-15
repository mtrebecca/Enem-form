<?php

namespace App\Modules\Treino\Http\Controllers;

use App\Modules\Treino\Http\Requests\ResponderTreinoRequest;
use App\Modules\Treino\Services\TreinoService;
use App\Support\DomainException;
use App\Support\QuestaoRespostaEvaluator;
use App\Support\RequestUserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreinoController
{
    public function __construct(
        private readonly TreinoService $treinoService,
        private readonly QuestaoRespostaEvaluator $respostaEvaluator,
    ) {}

    public function disciplinas(): JsonResponse
    {
        return response()->json($this->treinoService->disciplinasDisponiveis());
    }

    public function questaoAleatoria(Request $request): JsonResponse
    {
        $disciplina = $this->normalizarDisciplinaQuery($request->query('disciplina'));
        $excluir = $this->normalizarExcluirQuery($request->query('excluir', []));

        $payload = $this->treinoService->questaoAleatoria($disciplina, $excluir);
        if (!$payload) {
            throw new DomainException(
                'Nao ha questoes disponiveis com esse filtro ou todas ja foram sorteadas nesta rodada.',
                404,
                'HTTP_404'
            );
        }

        return response()->json($payload);
    }

    public function responder(ResponderTreinoRequest $request): JsonResponse
    {
        RequestUserId::require($request);
        $payload = $request->validated();
        $questaoId = (int) $payload['questao_id'];
        $questao = $this->treinoService->questaoDoPool($questaoId);

        if (! $questao) {
            throw new DomainException('Questao nao encontrada para treino.', 404, 'HTTP_404');
        }

        $avaliacao = $this->respostaEvaluator->avaliar($questao, $payload);
        if (! $avaliacao) {
            throw new DomainException('Opcao de resposta invalida.');
        }

        return response()->json($this->respostaEvaluator->jsonRespostaQuestao('Resposta avaliada', $questaoId, $avaliacao));
    }

    private function normalizarDisciplinaQuery(mixed $raw): ?string
    {
        if (!is_string($raw)) {
            return null;
        }
        $t = trim($raw);

        return $t === '' ? null : $t;
    }

    private function normalizarExcluirQuery(mixed $raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }

        return $raw !== null && $raw !== '' ? [(string) $raw] : [];
    }
}
