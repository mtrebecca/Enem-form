import React from 'react';
import QuestaoInterativa from '../components/QuestaoInterativa';

export default function TreinoScreen({
  s,
  loading,
  treinoDisciplinas,
  treinoFiltro,
  treinoQuestao,
  treinoRespostas,
  treinoFeedback,
  onBackDashboard,
  aplicarFiltroTreino,
  reiniciarRodadaTreino,
  responderTreino,
  proximaQuestaoTreino,
}) {
  return (
    <section className={s.card}>
      <div className="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 className="text-xl font-semibold">Treino aleatorio</h2>
          <p className={`text-sm ${s.muted}`}>
            Uma questao por vez. Use &quot;Proxima&quot; para sortear outra sem repetir nesta rodada. &quot;Nova rodada&quot; zera repeticoes.
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
            <option value="todas">Todas as areas</option>
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

      {treinoQuestao && (
        <>
          <QuestaoInterativa
            s={s}
            questao={treinoQuestao}
            feedbackMap={treinoFeedback}
            respostasMap={treinoRespostas}
            onOpcao={responderTreino}
          />
          <button
            type="button"
            className={`${s.btnPrimary} mt-6`}
            onClick={proximaQuestaoTreino}
            disabled={loading || !treinoFeedback[treinoQuestao.id]}
          >
            Proxima questao
          </button>
        </>
      )}

      {!loading && !treinoQuestao && (
        <p className={`text-sm ${s.muted}`}>
          Nao foi possivel carregar outra questao. Tente &quot;Nova rodada&quot; ou outro filtro de disciplina.
        </p>
      )}
    </section>
  );
}
