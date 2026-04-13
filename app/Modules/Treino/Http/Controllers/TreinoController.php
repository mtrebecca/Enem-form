<?php

namespace App\Modules\Treino\Http\Controllers;

use App\Modules\Provas\Services\ProvasService;
use App\Modules\Treino\Services\TreinoService;
use App\Support\RequestUserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TreinoController
{
    public function __construct(
        private readonly TreinoService $treinoService,
        private readonly ProvasService $provasService,
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
            return response()->json([
                'message' => 'Nao ha questoes disponiveis com esse filtro ou todas ja foram sorteadas nesta rodada.',
                'restantes' => $this->treinoService->totalNoPool($disciplina, $excluir),
            ], 404);
        }

        return response()->json($payload);
    }

    public function responder(Request $request): JsonResponse
    {
        RequestUserId::require($request);

        return response()->json(
            $this->provasService->responderTreinoLivre(
                (int) $request->input('questao_id'),
                $request->all()
            )
        );
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
