import React from 'react';
import QuestaoInterativa from '../components/QuestaoInterativa';

export default function ProvaScreen({
  s,
  provaAtual,
  questoes,
  questoesMeta,
  loading,
  feedbackPorQuestao,
  respostas,
  onBackDashboard,
  responder,
  carregarPaginaQuestoes,
  finalizarProva,
}) {
  return (
    <section className={s.card}>
      <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold">{provaAtual.titulo}</h2>
          <p className={`text-sm ${s.muted}`}>
            Modo estudo: ao marcar uma alternativa, voce ve na hora se acertou e o gabarito fica destacado em verde. No Enem real nao ha esse
            retorno — aqui o foco e aprender. Pode mudar de pagina sem marcar; em branco conta como erro ao finalizar.
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
          Pagina {questoesMeta.page} de {questoesMeta.total_pages} ({questoesMeta.total} questoes)
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
            Proxima
          </button>
        </div>
      </div>

      <button type="button" className={`${s.btnPrimary} mt-6`} onClick={finalizarProva} disabled={loading}>
        {loading ? 'Finalizando...' : 'Finalizar prova'}
      </button>
    </section>
  );
}
