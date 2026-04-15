<?php

namespace App\Support;

use App\Modules\Provas\Models\Prova;
use App\Modules\Provas\Models\Questao;

class ActiveProvasCatalog
{
    public function listar(): array
    {
        $provas = $this->queryProvasSimuladoNoDashboard()->orderBy('id')->get();
        if ($provas->isEmpty()) {
            return [];
        }

        $ids = $provas->pluck('id')->all();
        $disciplinasPorProva = Questao::query()
            ->select('prova_id', 'disciplina')
            ->whereIn('prova_id', $ids)
            ->distinct()
            ->orderBy('disciplina')
            ->get()
            ->groupBy('prova_id')
            ->map(fn ($grupo) => $grupo->pluck('disciplina')->unique()->values()->all());

        return $provas
            ->map(fn (Prova $prova): array => $this->formatProvaResumo($prova, $disciplinasPorProva->get($prova->id, [])))
            ->values()
            ->all();
    }

    public function detalhe(int $id): ?array
    {
        $prova = $this->queryProvasSimuladoNoDashboard()
            ->where('id', $id)
            ->first();

        return $prova ? $this->formatProvaResumo($prova) : null;
    }

    public function existeNoDashboard(int $id): bool
    {
        return $this->queryProvasSimuladoNoDashboard()->where('id', $id)->exists();
    }

    private function queryProvasSimuladoNoDashboard()
    {
        return Prova::query()
            ->where('status', 'ativo')
            ->where(function ($q): void {
                $q->whereNull('tipo')->orWhere('tipo', 'simulado');
            });
    }

    private function formatProvaResumo(Prova $prova, ?array $disciplinas = null): array
    {
        $disciplinas ??= $prova->questoes()
            ->select('disciplina')
            ->distinct()
            ->pluck('disciplina')
            ->values()
            ->all();

        return [
            'id' => $prova->id,
            'titulo' => $prova->titulo,
            'status' => $prova->status,
            'disciplinas' => $disciplinas,
        ];
    }
}
