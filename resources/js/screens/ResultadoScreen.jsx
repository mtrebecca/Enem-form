import React from 'react';

export default function ResultadoScreen({ s, resultado, acertosTotais, onVoltarDashboard, onLimparSessao }) {
  return (
    <section className={`${s.card} mx-auto w-full max-w-3xl`}>
      <h2 className="text-2xl font-bold">Resultado da prova #{resultado.prova_id}</h2>
      <p className={`mt-1 text-sm ${s.muted}`}>
        Total de acertos: {acertosTotais}
        {resultado.totais?.percentual_acerto != null && ` (${resultado.totais.percentual_acerto}%)`}
      </p>

      <div className="mt-5 grid gap-3 sm:grid-cols-2">
        {Object.entries(resultado.disciplinas).map(([disciplina, dados]) => (
          <article key={disciplina} className={s.innerCard}>
            <h3 className="font-semibold">{disciplina}</h3>
            <p className={`mt-2 ${s.disciplineOk}`}>Acertos: {dados.acertos}</p>
            <p className={s.disciplineBad}>Erros: {dados.erros}</p>
          </article>
        ))}
      </div>

      <div className="mt-6 flex flex-wrap gap-3">
        <button type="button" className={s.btnPrimary} onClick={onVoltarDashboard}>
          Voltar ao dashboard
        </button>
        <button type="button" className={s.btnGhost} onClick={onLimparSessao}>
          Limpar sessao atual
        </button>
      </div>
    </section>
  );
}
