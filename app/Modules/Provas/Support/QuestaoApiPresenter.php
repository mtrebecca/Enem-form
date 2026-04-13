<?php

namespace App\Modules\Provas\Support;

use App\Modules\Provas\Models\Questao;

final class QuestaoApiPresenter
{
    /**
     * @param  array<int, array{texto: string, correta?: bool}>  $opcoesBrutas
     * @return list<array{id: int, texto: string}>
     */
    public static function opcoesPublicas(array $opcoesBrutas): array
    {
        $out = [];
        foreach ($opcoesBrutas as $indice => $op) {
            $out[] = [
                'id' => (int) $indice,
                'texto' => $op['texto'],
            ];
        }

        return $out;
    }

    public static function fromModel(Questao $questao): array
    {
        return [
            'id' => $questao->id,
            'disciplina' => $questao->disciplina,
            'enunciado' => $questao->enunciado,
            'fonte' => $questao->fonte,
            'opcoes' => self::opcoesPublicas($questao->opcoes ?? []),
        ];
    }
}
