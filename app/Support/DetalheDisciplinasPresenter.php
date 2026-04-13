<?php

namespace App\Support;

final class DetalheDisciplinasPresenter
{
    /**
     * @param  iterable<int, array{disciplina: string, total_questoes: mixed, total_acertos: mixed}>  $linhas
     * @return array<string, array{acertos: int, erros: int}>
     */
    public static function mapHistoricoResumo(iterable $linhas): array
    {
        $disciplinas = [];
        foreach ($linhas as $linha) {
            $nome = $linha['disciplina'];
            $total = (int) $linha['total_questoes'];
            $acertos = (int) $linha['total_acertos'];
            $disciplinas[$nome] = [
                'acertos' => $acertos,
                'erros' => $total - $acertos,
            ];
        }

        return $disciplinas;
    }

    /**
     * @param  iterable<int, array{disciplina: string, total_questoes: mixed, total_acertos: mixed, percentual_acerto: mixed}>  $linhas
     * @return array<string, array{acertos: int, erros: int, total_questoes: int, percentual_acerto: float}>
     */
    public static function mapResultadoPorProva(iterable $linhas): array
    {
        $disciplinas = [];
        foreach ($linhas as $linha) {
            $nome = $linha['disciplina'];
            $total = (int) $linha['total_questoes'];
            $acertos = (int) $linha['total_acertos'];
            $disciplinas[$nome] = [
                'acertos' => $acertos,
                'erros' => $total - $acertos,
                'total_questoes' => $total,
                'percentual_acerto' => (float) $linha['percentual_acerto'],
            ];
        }

        return $disciplinas;
    }
}
