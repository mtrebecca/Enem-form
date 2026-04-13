<?php

namespace App\Modules\Users\Services;

use App\Modules\Resultados\Models\Resultado;
use App\Modules\Users\Models\User;
use App\Support\DetalheDisciplinasPresenter;

class UsersService
{
    public function minhaConta(int $userId): array
    {
        $user = User::query()->find($userId);
        if (! $user) {
            return ['id' => 0, 'nome' => 'Usuario nao encontrado', 'email' => ''];
        }

        return [
            'id' => $user->id,
            'nome' => $user->name,
            'email' => $user->email,
        ];
    }

    public function historico(int $userId): array
    {
        return Resultado::query()
            ->with('sessao:id,prova_id,finalizada_em,user_id')
            ->whereHas('sessao', fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Resultado $r): array {
                return [
                    'prova_id' => $r->sessao?->prova_id,
                    'sessao_id' => $r->sessao_id,
                    'finalizada_em' => $r->sessao?->finalizada_em?->toDateTimeString(),
                    'totais' => [
                        'total_questoes' => $r->total_questoes,
                        'total_acertos' => $r->total_acertos,
                        'percentual_acerto' => (float) $r->percentual_acerto,
                    ],
                    'disciplinas' => DetalheDisciplinasPresenter::mapHistoricoResumo($r->detalhe_disciplinas ?? []),
                ];
            })
            ->values()
            ->all();
    }
}
