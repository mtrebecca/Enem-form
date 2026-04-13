<?php

namespace App\Modules\Provas\Services;

use App\Modules\Provas\Models\Prova;
use App\Modules\Provas\Models\Questao;
use App\Modules\Provas\Models\SessaoProva;
use App\Modules\Provas\Support\QuestaoApiPresenter;

class ProvasService
{
    public function __construct(
        private readonly ProvaFinalizacaoService $finalizacao,
    ) {}

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

    public function iniciarRespostaApi(int $provaId, int $userId, array $expand, int $perPage): array
    {
        $sessao = $this->iniciar($provaId, $userId);
        if ($expand === []) {
            return $sessao;
        }

        $payload = ['sessao' => $sessao];
        if (in_array('prova', $expand, true)) {
            $prova = $this->detalhe($provaId);
            if ($prova !== null) {
                $payload['prova'] = $prova;
            }
        }
        if (in_array('questoes', $expand, true)) {
            $payload['questoes'] = $this->questoes($provaId, 1, $perPage);
        }

        return $payload;
    }

    public function iniciar(int $provaId, int $userId): array
    {
        $prova = $this->queryProvasSimuladoNoDashboard()
            ->where('id', $provaId)
            ->first();
        if (! $prova) {
            abort(404, 'Prova nao encontrada ou inativa.');
        }

        $existente = SessaoProva::query()
            ->where('user_id', $userId)
            ->where('prova_id', $provaId)
            ->where('status', 'em_andamento')
            ->first();

        if ($existente) {
            return $this->formatSessao($existente);
        }

        $sessao = SessaoProva::query()->create([
            'user_id' => $userId,
            'prova_id' => $provaId,
            'status' => 'em_andamento',
            'iniciada_em' => now(),
            'respostas' => [],
        ]);

        return $this->formatSessao($sessao);
    }

    public function questoes(int $provaId, int $page = 1, int $perPage = 2): array
    {
        if (! $this->queryProvasSimuladoNoDashboard()->where('id', $provaId)->exists()) {
            abort(404, 'Prova nao encontrada.');
        }

        $perPage = max(1, min($perPage, 3));
        $page = max(1, $page);

        $base = Questao::query()->where('prova_id', $provaId)->orderBy('id');
        $total = (clone $base)->count();
        $items = (clone $base)->forPage($page, $perPage)->get();

        return [
            'data' => $items->map(fn (Questao $questao): array => QuestaoApiPresenter::fromModel($questao))->values()->all(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) max(1, ceil($total / $perPage)),
            ],
        ];
    }

    public function responder(int $provaId, int $userId, array $payload): ?array
    {
        $sessao = $this->sessaoEmAndamento($userId, $provaId);
        if (! $sessao) {
            return null;
        }

        $questaoId = (int) ($payload['questao_id'] ?? 0);
        $questao = Questao::query()->where('prova_id', $provaId)->where('id', $questaoId)->first();
        if (! $questao) {
            abort(422, 'Questao invalida para esta prova.');
        }

        $escolha = $this->resolverAlternativa($questao, $payload);
        if (! $escolha) {
            abort(422, 'Opcao de resposta invalida.');
        }

        $mapa = $sessao->respostas ?? [];
        $mapa[(string) $questaoId] = $escolha['texto'];
        $sessao->update(['respostas' => $mapa]);

        return $this->jsonRespostaQuestao('Resposta salva com sucesso', $questaoId, $escolha, $questao->opcoes ?? []);
    }

    public function finalizar(int $provaId, int $userId): ?array
    {
        $sessao = $this->sessaoEmAndamento($userId, $provaId);
        if (! $sessao) {
            return null;
        }

        return $this->finalizacao->finalizar($sessao, $provaId, $userId);
    }

    public function responderTreinoLivre(int $questaoId, array $payload): array
    {
        $questao = Questao::query()->find($questaoId);
        if (! $questao || ! $this->questaoPertenceAoPoolTreino($questao)) {
            abort(404, 'Questao nao encontrada para treino.');
        }

        $escolha = $this->resolverAlternativa($questao, $payload);
        if (! $escolha) {
            abort(422, 'Opcao de resposta invalida.');
        }

        return $this->jsonRespostaQuestao('Resposta avaliada', $questaoId, $escolha, $questao->opcoes ?? []);
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

    private function formatSessao(SessaoProva $sessao): array
    {
        return [
            'id' => $sessao->id,
            'prova_id' => $sessao->prova_id,
            'user_id' => $sessao->user_id,
            'iniciada_em' => $sessao->iniciada_em?->toDateTimeString(),
            'status' => $sessao->status,
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

    private function resolverAlternativa(Questao $questao, array $payload): ?array
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
            ];
        }

        $texto = trim((string) ($payload['resposta'] ?? ''));
        foreach ($opcoes as $indice => $op) {
            if ($op['texto'] === $texto) {
                return [
                    'texto' => $op['texto'],
                    'correta' => (bool) $op['correta'],
                    'indice' => $indice,
                ];
            }
        }

        return null;
    }

    private function sessaoEmAndamento(int $userId, int $provaId): ?SessaoProva
    {
        return SessaoProva::query()
            ->where('user_id', $userId)
            ->where('prova_id', $provaId)
            ->where('status', 'em_andamento')
            ->first();
    }

    private function jsonRespostaQuestao(string $message, int $questaoId, array $escolha, array $opcoes): array
    {
        return [
            'message' => $message,
            'questao_id' => $questaoId,
            'opcao_id' => $escolha['indice'],
            'texto' => $escolha['texto'],
            'feedback' => [
                'acertou' => $escolha['correta'],
                'gabarito_opcao_id' => $this->indiceAlternativaCorreta($opcoes),
            ],
        ];
    }

    private function questaoPertenceAoPoolTreino(Questao $questao): bool
    {
        return Prova::query()
            ->where('id', $questao->prova_id)
            ->where('status', 'ativo')
            ->exists();
    }
}
