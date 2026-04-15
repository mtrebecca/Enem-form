import React from 'react';
import QuestaoInterativa from '../components/QuestaoInterativa';

export default function TreinoScreen({
  s,
  loading,
  flow,
  onBackDashboard,
}) {
  const {
    treinoDisciplinas,
    treinoFiltro,
    treinoQuestao,
    treinoRespostas,
    treinoFeedback,
    treinoResumoRodada,
    treinoRodadaFinalizada,
    treinoEhUltimaQuestao,
    aplicarFiltroTreino,
    reiniciarRodadaTreino,
    responderTreino,
    proximaQuestaoTreino,
    finalizarRodadaTreino,
  } = flow;

  return (
    <section className={s.card}>
      <div className="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold">Treino aleatório</h2>
          <p className={`text-sm ${s.muted}`}>
            Uma questão por vez. Use &quot;Próxima&quot; para sortear outra sem repetir nesta rodada. &quot;Nova rodada&quot; zera repetições.
          </p>
        </div>
        <button type="button" className={s.btnGhost} onClick={onBackDashboard}>
          Voltar ao dashboard
        </button>
      </div>

      <div className={`mb-6 flex flex-wrap items-end gap-3 ${s.innerCard}`}>
        <label className={`flex flex-col gap-1 text-sm ${s.sub}`}>
          Disciplina
          <select
            className={s.input}
            value={treinoFiltro}
            onChange={(e) => aplicarFiltroTreino(e.target.value)}
            disabled={loading}
          >
            <option value="todas">Todas as áreas</option>
            {treinoDisciplinas.map((d) => (
              <option key={d} value={d}>
                {d}
              </option>
            ))}
          </select>
        </label>
        <button type="button" className={s.btnGhost} onClick={reiniciarRodadaTreino} disabled={loading}>
          Nova rodada
        </button>
      </div>

      {treinoRodadaFinalizada && (
        <article className={`${s.innerCard} mb-6`} role="status">
          <h3 className="text-lg font-semibold">Resultado da rodada de treino</h3>
          <p className={`mt-2 text-sm ${s.sub}`}>
            Questões respondidas: <strong>{treinoResumoRodada.respondidas}</strong>
          </p>
          <p className={`mt-1 text-sm ${s.sub}`}>
            Acertos: <strong>{treinoResumoRodada.acertos}</strong>
          </p>
        </article>
      )}

      {!treinoRodadaFinalizada && treinoQuestao && (
        <>
          <QuestaoInterativa
            s={s}
            questao={treinoQuestao}
            feedbackMap={treinoFeedback}
            respostasMap={treinoRespostas}
            onOpcao={responderTreino}
          />
          <div className="mt-6 flex flex-wrap gap-3">
            <button
              type="button"
              className={s.btnGhost}
              onClick={proximaQuestaoTreino}
              disabled={loading || !treinoFeedback[treinoQuestao.id] || treinoEhUltimaQuestao}
            >
              Próxima questão
            </button>
            <button
              type="button"
              className={s.btnPrimary}
              onClick={finalizarRodadaTreino}
              disabled={loading || treinoResumoRodada.respondidas < 1}
            >
              Finalizar rodada
            </button>
          </div>
        </>
      )}

    </section>
  );
}
