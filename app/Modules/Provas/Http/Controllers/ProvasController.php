<?php

namespace App\Modules\Provas\Http\Controllers;

use App\Modules\Provas\Http\Requests\DefinirRespostaRequest;
use App\Modules\Provas\Services\ProvasService;
use App\Support\RequestUserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProvasController
{
    public function __construct(private readonly ProvasService $provasService) {}

    public function index(): JsonResponse
    {
        return response()->json($this->provasService->listar());
    }

    public function show(int $id): JsonResponse
    {
        $prova = $this->provasService->detalhe($id);

        if (!$prova) {
            abort(404, 'Prova nao encontrada');
        }

        return response()->json($prova);
    }

    public function iniciar(Request $request, int $id): JsonResponse
    {
        $userId = RequestUserId::require($request);
        $expandRaw = (string) $request->query('expand', '');
        $expand = array_values(array_filter(array_map('trim', explode(',', $expandRaw))));
        $perPage = (int) $request->query('per_page', 3);

        return response()->json($this->provasService->iniciarRespostaApi($id, $userId, $expand, $perPage));
    }

    public function questoes(Request $request, int $id): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 2);

        return response()->json($this->provasService->questoes($id, $page, $perPage));
    }

    /**
     * Substitui a resposta da questão na sessão em andamento (primeira escolha ou correção).
     */
    public function definirResposta(DefinirRespostaRequest $request, int $id, int $questao): JsonResponse
    {
        $userId = RequestUserId::require($request);
        $payload = array_merge($request->validated(), ['questao_id' => $questao]);
        $resultado = $this->provasService->responder($id, $userId, $payload);

        if (! $resultado) {
            abort(422, 'Sessao de prova nao iniciada');
        }

        return response()->json($resultado);
    }

    public function finalizar(Request $request, int $id): JsonResponse
    {
        $userId = RequestUserId::require($request);
        $resultado = $this->provasService->finalizar($id, $userId);

        if (!$resultado) {
            abort(422, 'Sessao de prova nao iniciada');
        }

        return response()->json($resultado);
    }
}
