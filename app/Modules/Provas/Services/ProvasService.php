<?php

namespace App\Modules\Provas\Services;

use App\Modules\Provas\Models\Prova;
use App\Modules\Provas\Models\Questao;
use App\Modules\Provas\Models\SessaoProva;
use App\Modules\Provas\Support\QuestaoApiPresenter;
use App\Support\ActiveProvasCatalog;
use App\Support\DomainException;
use App\Support\QuestaoRespostaEvaluator;

class ProvasService
{
    public function __construct(
        private readonly ProvaFinalizacaoService $finalizacao,
        private readonly ActiveProvasCatalog $catalog,
        private readonly QuestaoRespostaEvaluator $respostaEvaluator,
    ) {}

    public function listar(): array
    {
        return $this->catalog->listar();
    }

    public function detalhe(int $id): ?array
    {
        return $this->catalog->detalhe($id);
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
        if (! $this->catalog->existeNoDashboard($provaId)) {
            throw new DomainException('Prova nao encontrada ou inativa.', 404, 'HTTP_404');
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
        if (! $this->catalog->existeNoDashboard($provaId)) {
            throw new DomainException('Prova nao encontrada.', 404, 'HTTP_404');
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
            throw new DomainException('Questao invalida para esta prova.');
        }

        $escolha = $this->respostaEvaluator->avaliar($questao, $payload);
        if (! $escolha) {
            throw new DomainException('Opcao de resposta invalida.');
        }

        $mapa = $sessao->respostas ?? [];
        $mapa[(string) $questaoId] = $escolha['texto'];
        $sessao->update(['respostas' => $mapa]);

        return $this->respostaEvaluator->jsonRespostaQuestao('Resposta salva com sucesso', $questaoId, $escolha);
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
            throw new DomainException('Questao nao encontrada para treino.', 404, 'HTTP_404');
        }

        $escolha = $this->respostaEvaluator->avaliar($questao, $payload);
        if (! $escolha) {
            throw new DomainException('Opcao de resposta invalida.');
        }

        return $this->respostaEvaluator->jsonRespostaQuestao('Resposta avaliada', $questaoId, $escolha);
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

    private function sessaoEmAndamento(int $userId, int $provaId): ?SessaoProva
    {
        return SessaoProva::query()
            ->where('user_id', $userId)
            ->where('prova_id', $provaId)
            ->where('status', 'em_andamento')
            ->first();
    }

    private function questaoPertenceAoPoolTreino(Questao $questao): bool
    {
        return Prova::query()
            ->where('id', $questao->prova_id)
            ->where('status', 'ativo')
            ->exists();
    }
}
