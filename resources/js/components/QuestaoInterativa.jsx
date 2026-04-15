import { feedbackTexto, opcaoClassNames } from '../lib/opcaoClasses';

export default function QuestaoInterativa({ s, questao, feedbackMap, respostasMap, onOpcao }) {
  const fb = feedbackMap[questao.id];

  return (
    <article className={s.innerCard}>
      <p className={`mb-1 text-xs font-medium uppercase tracking-wide ${s.muted}`}>{questao.disciplina}</p>
      <p className={`mb-3 text-sm ${s.sub}`}>{questao.enunciado}</p>
      {questao.fonte && <p className={`mb-3 text-xs italic ${s.muted}`}>Referência: {questao.fonte}</p>}
      <div className="flex flex-col gap-2">
        {questao.opcoes.map((opcao) => (
          <button
            key={opcao.id}
            type="button"
            className={`rounded-lg px-4 py-2.5 text-left text-sm font-semibold transition ${opcaoClassNames(
              s,
              feedbackMap,
              respostasMap,
              questao.id,
              opcao
            )}`}
            onClick={() => onOpcao(questao.id, opcao)}
          >
            {opcao.texto}
          </button>
        ))}
      </div>
      {fb && (
        <div className={fb.acertou ? s.feedbackOk : s.feedbackBad} role="status">
          {feedbackTexto(fb.acertou)}
        </div>
      )}
    </article>
  );
}
