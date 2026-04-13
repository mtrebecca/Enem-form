<?php

namespace App\Modules\Resultados\Services;

use App\Modules\Resultados\Models\Resultado;
use App\Support\DetalheDisciplinasPresenter;

class ResultadosService
{
    public function porProva(int $provaId, int $userId): ?array
    {
        $resultado = Resultado::query()
            ->with('sessao:id,prova_id,user_id')
            ->whereHas('sessao', function ($q) use ($provaId, $userId): void {
                $q->where('prova_id', $provaId)->where('user_id', $userId);
            })
            ->orderByDesc('id')
            ->first();

        if (! $resultado) {
            return null;
        }

        return [
            'prova_id' => $provaId,
            'user_id' => $userId,
            'sessao_id' => $resultado->sessao_id,
            'disciplinas' => DetalheDisciplinasPresenter::mapResultadoPorProva($resultado->detalhe_disciplinas ?? []),
            'totais' => [
                'total_questoes' => $resultado->total_questoes,
                'total_acertos' => $resultado->total_acertos,
                'percentual_acerto' => (float) $resultado->percentual_acerto,
            ],
            'created_at' => $resultado->created_at?->toDateTimeString(),
        ];
    }
}
