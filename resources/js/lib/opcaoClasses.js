export function opcaoClassNames(styles, feedbackMap, respostasMap, questaoId, opcao) {
  const fb = feedbackMap[questaoId];
  const selected = respostasMap[questaoId] === opcao.texto;
  if (!fb) {
    return selected ? styles.optionOn : styles.optionIdle;
  }
  const gabarito = opcao.id === fb.gabarito_opcao_id;
  const acertou = selected && fb.acertou;
  const errou = selected && !fb.acertou;
  if (acertou) return styles.optionOn;
  if (errou) return `${styles.optionIdle} ${styles.optionWrongReveal}`;
  if (gabarito) return `${styles.optionIdle} ${styles.optionGabaritoReveal}`;
  return `${styles.optionIdle} ${styles.optionDimmed}`;
}

export function feedbackTexto(acertou) {
  return acertou ? 'Correto.' : 'Incorreto. O gabarito está destacado em verde.';
}
