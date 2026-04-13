<?php

namespace App\Modules\Provas\Services;

use App\Modules\Provas\Models\Questao;
use App\Modules\Provas\Models\SessaoProva;
use App\Modules\Resultados\Models\Resultado;
use Illuminate\Support\Facades\DB;

class ProvaFinalizacaoService
{
    public function finalizar(SessaoProva $sessao, int $provaId, int $userId): array
    {
        $questoes = Questao::query()->where('prova_id', $provaId)->orderBy('id')->get();
        $mapaRespostas = $sessao->respostas ?? [];

        $totalQuestoes = $questoes->count();
        $totalAcertos = 0;
        $agregado = [];

        foreach ($questoes as $questao) {
            $nome = $questao->disciplina;
            if (! isset($agregado[$nome])) {
                $agregado[$nome] = ['total' => 0, 'acertos' => 0];
            }
            $agregado[$nome]['total']++;

            $letra = $mapaRespostas[(string) $questao->id] ?? null;
            if ($this->respostaEstaCorreta($questao, $letra)) {
                $totalAcertos++;
                $agregado[$nome]['acertos']++;
            }
        }

        $percentual = $totalQuestoes > 0 ? round(100 * $totalAcertos / $totalQuestoes, 2) : 0.0;

        $porDisciplina = [];
        $detalheDisciplinas = [];
        foreach ($agregado as $disciplina => $stats) {
            $t = $stats['total'];
            $a = $stats['acertos'];
            $porDisciplina[$disciplina] = [
                'acertos' => $a,
                'erros' => $t - $a,
            ];
            $detalheDisciplinas[] = [
                'disciplina' => $disciplina,
                'total_questoes' => $t,
                'total_acertos' => $a,
                'percentual_acerto' => $t > 0 ? round(100 * $a / $t, 2) : 0.0,
            ];
        }

        DB::transaction(function () use ($sessao, $totalQuestoes, $totalAcertos, $percentual, $detalheDisciplinas): void {
            $sessao->update([
                'status' => 'finalizada',
                'finalizada_em' => now(),
            ]);

            Resultado::query()->create([
                'sessao_id' => $sessao->id,
                'total_questoes' => $totalQuestoes,
                'total_acertos' => $totalAcertos,
                'percentual_acerto' => $percentual,
                'detalhe_disciplinas' => $detalheDisciplinas,
                'created_at' => now(),
            ]);
        });

        return [
            'prova_id' => $provaId,
            'user_id' => $userId,
            'status' => 'finalizada',
            'disciplinas' => $porDisciplina,
            'finalizada_em' => now()->toDateTimeString(),
            'totais' => [
                'total_questoes' => $totalQuestoes,
                'total_acertos' => $totalAcertos,
                'percentual_acerto' => $percentual,
            ],
        ];
    }

    private function respostaEstaCorreta(Questao $questao, ?string $letraRespondida): bool
    {
        if ($letraRespondida === null || $letraRespondida === '') {
            return false;
        }

        foreach ($questao->opcoes ?? [] as $op) {
            if ($op['texto'] === $letraRespondida) {
                return (bool) $op['correta'];
            }
        }

        return false;
    }
}
