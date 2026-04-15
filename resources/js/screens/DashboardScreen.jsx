import React from 'react';

export default function DashboardScreen({ s, provas, historico, user, loading, onTreino, onIniciarProva }) {
  return (
    <section className="grid gap-6 lg:grid-cols-[2fr_1fr]">
      <div className={`${s.card} lg:col-span-2`}>
        <h2 className="mb-2 text-lg font-semibold">Treino aleatório</h2>
        <p className={`mb-4 text-sm ${s.muted}`}>
          Questões sorteadas de todas as áreas (banco + simulados). Filtre por disciplina ou misture tudo. Referência de conferência
          aparece em cada item quando houver.
        </p>
        <button type="button" className={s.btnPrimary} onClick={onTreino} disabled={loading}>
          Iniciar treino aleatório
        </button>
      </div>

      <div className={s.card}>
        <h2 className="mb-3 text-xl font-semibold">Provas disponíveis</h2>
        <p className={`mb-6 text-sm ${s.muted}`}>Simulados completos (lista oficial na prova).</p>
        <div className="space-y-3">
          {provas.map((prova) => (
            <article key={prova.id} className={s.innerCard}>
              <h3 className="font-semibold">{prova.titulo}</h3>
              <p className={`mt-1 text-sm ${s.muted}`}>Disciplinas: {prova.disciplinas.join(', ')}</p>
              <button type="button" className={`${s.btnPrimary} mt-3`} onClick={() => onIniciarProva(prova.id)} disabled={loading}>
                Iniciar prova
              </button>
            </article>
          ))}
        </div>
      </div>

      <div className={s.card}>
        <h2 className="mb-3 text-xl font-semibold">Minha conta</h2>
        <p className={`text-sm ${s.sub}`}>{user?.nome}</p>
        <p className={`mb-6 text-sm ${s.muted}`}>{user?.email}</p>
        <h3 className={`mb-2 text-sm font-semibold uppercase tracking-wider ${s.sub}`}>Histórico</h3>
        <div className="space-y-2">
          {historico.length === 0 && <p className={`text-sm ${s.muted}`}>Nenhuma prova finalizada ainda.</p>}
          {historico.map((item) => (
            <div key={`${item.prova_id}-${item.sessao_id}`} className={s.histItem}>
              <p>Prova #{item.prova_id}</p>
              <p className={s.histMeta}>{item.finalizada_em}</p>
              {item.totais && (
                <p className={s.histSmall}>
                  {item.totais.total_acertos}/{item.totais.total_questoes} acertos
                </p>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
