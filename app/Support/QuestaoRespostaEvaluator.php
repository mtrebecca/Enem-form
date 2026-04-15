<?php

namespace App\Support;

use App\Modules\Provas\Models\Questao;

class QuestaoRespostaEvaluator
{
    public function avaliar(Questao $questao, array $payload): ?array
    {
        $opcoes = $questao->opcoes ?? [];
        if ($opcoes === []) {
            return null;
        }

        if (array_key_exists('opcao_id', $payload) && $payload['opcao_id'] !== '' && $payload['opcao_id'] !== null) {
            $indice = (int) $payload['opcao_id'];
            if (! isset($opcoes[$indice])) {
                return null;
            }
            $op = $opcoes[$indice];

            return [
                'texto' => $op['texto'],
                'correta' => (bool) $op['correta'],
                'indice' => $indice,
                'gabarito_opcao_id' => $this->indiceAlternativaCorreta($opcoes),
            ];
        }

        $texto = trim((string) ($payload['resposta'] ?? ''));
        foreach ($opcoes as $indice => $op) {
            if ($op['texto'] === $texto) {
                return [
                    'texto' => $op['texto'],
                    'correta' => (bool) $op['correta'],
                    'indice' => $indice,
                    'gabarito_opcao_id' => $this->indiceAlternativaCorreta($opcoes),
                ];
            }
        }

        return null;
    }

    public function jsonRespostaQuestao(string $message, int $questaoId, array $avaliacao): array
    {
        return [
            'message' => $message,
            'questao_id' => $questaoId,
            'opcao_id' => $avaliacao['indice'],
            'texto' => $avaliacao['texto'],
            'feedback' => [
                'acertou' => $avaliacao['correta'],
                'gabarito_opcao_id' => $avaliacao['gabarito_opcao_id'],
            ],
        ];
    }

    private function indiceAlternativaCorreta(array $opcoes): ?int
    {
        foreach ($opcoes as $indice => $op) {
            if (! empty($op['correta'])) {
                return (int) $indice;
            }
        }

        return null;
    }
}
