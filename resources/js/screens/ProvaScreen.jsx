import React from 'react';
import QuestaoInterativa from '../components/QuestaoInterativa';

export default function ProvaScreen({
  s,
  loading,
  flow,
  onBackDashboard,
  onFinalizarProva,
}) {
  const { provaAtual, questoes, questoesMeta, feedbackPorQuestao, respostas, responder, carregarPaginaQuestoes } = flow;

  return (
    <section className={s.card}>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold">{provaAtual.titulo}</h2>
          <p className={`text-sm ${s.muted}`}>
            Modo estudo: ao marcar uma alternativa, você vê na hora se acertou e o gabarito fica destacado em verde. No Enem real não há esse
            retorno — aqui o foco é aprender. Pode mudar de página sem marcar; em branco conta como erro ao finalizar.
          </p>
        </div>
        <button type="button" className={s.btnGhost} onClick={onBackDashboard}>
          Voltar ao dashboard
        </button>
      </div>

      <div className="space-y-4">
        {questoes.map((questao) => (
          <QuestaoInterativa
            key={questao.id}
            s={s}
            questao={questao}
            feedbackMap={feedbackPorQuestao}
            respostasMap={respostas}
            onOpcao={responder}
          />
        ))}
      </div>

      <div className={s.pagerBar}>
        <p className={s.muted}>
          Página {questoesMeta.page} de {questoesMeta.total_pages} ({questoesMeta.total} questões)
        </p>
        <div className="flex gap-2">
          <button
            type="button"
            className={s.btnGhost}
            disabled={loading || questoesMeta.page <= 1}
            onClick={() => carregarPaginaQuestoes(questoesMeta.page - 1)}
          >
            Anterior
          </button>
          <button
            type="button"
            className={s.btnGhost}
            disabled={loading || questoesMeta.page >= questoesMeta.total_pages}
            onClick={() => carregarPaginaQuestoes(questoesMeta.page + 1)}
          >
            Próxima questão
          </button>
          <button type="button" className={s.btnPrimary} onClick={onFinalizarProva} disabled={loading}>
            {loading ? 'Finalizando...' : 'Finalizar prova'}
          </button>
        </div>
      </div>
    </section>
  );
}
