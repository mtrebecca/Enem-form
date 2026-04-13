<?php

namespace App\Modules\Resultados\Http\Controllers;

use App\Modules\Resultados\Services\ResultadosService;
use App\Support\RequestUserId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultadosController
{
    public function __construct(private readonly ResultadosService $resultadosService) {}

    public function show(Request $request, int $provaId): JsonResponse
    {
        $userId = RequestUserId::require($request);
        $resultado = $this->resultadosService->porProva($provaId, $userId);

        if (!$resultado) {
            return response()->json(['message' => 'Resultado nao encontrado para esta prova'], 404);
        }

        return response()->json($resultado);
    }
}
