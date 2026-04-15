<?php

namespace App\Modules\Treino\Services;

use App\Modules\Provas\Models\Prova;
use App\Modules\Provas\Models\Questao;
use App\Modules\Provas\Support\QuestaoApiPresenter;

class TreinoService
{
    public function disciplinasDisponiveis(): array
    {
        return Questao::query()
            ->whereHas('prova', fn ($q) => $q->where('status', 'ativo'))
            ->distinct()
            ->orderBy('disciplina')
            ->pluck('disciplina')
            ->values()
            ->all();
    }

    public function totalNoPool(?string $disciplina, array $excluirIds): int
    {
        return $this->baseQuery($disciplina, $excluirIds)->count();
    }

    public function questaoAleatoria(?string $disciplina, array $excluirIds): ?array
    {
        $query = $this->baseQuery($disciplina, $excluirIds);

        $questao = $query->inRandomOrder()->first();
        if (! $questao) {
            return null;
        }

        return QuestaoApiPresenter::fromModel($questao);
    }

    public function questaoDoPool(int $questaoId): ?Questao
    {
        return Questao::query()
            ->where('id', $questaoId)
            ->whereHas('prova', function ($q): void {
                $q->where('status', 'ativo');
            })
            ->first();
    }

    private function baseQuery(?string $disciplina, array $excluirIds)
    {
        $query = Questao::query()
            ->whereHas('prova', function ($q): void {
                $q->where('status', 'ativo');
            });

        if ($disciplina !== null && $disciplina !== '' && $disciplina !== 'todas') {
            $query->where('disciplina', $disciplina);
        }

        $excluirIds = array_values(array_unique(array_filter(array_map('intval', $excluirIds))));
        if ($excluirIds !== []) {
            $query->whereNotIn('id', $excluirIds);
        }

        return $query;
    }
}
