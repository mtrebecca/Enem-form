import { describe, expect, it } from 'vitest';
import { feedbackTexto, opcaoClassNames } from './opcaoClasses';

const styles = {
  optionOn: 'on',
  optionIdle: 'idle',
  optionWrongReveal: 'wrong',
  optionGabaritoReveal: 'gabarito',
  optionDimmed: 'dim',
};

describe('feedbackTexto', () => {
  it('mensagem para acerto', () => {
    expect(feedbackTexto(true)).toBe('Correto.');
  });

  it('mensagem para erro', () => {
    expect(feedbackTexto(false)).toContain('Incorreto');
  });
});

describe('opcaoClassNames', () => {
  const opcao = { id: 2, texto: 'B' };

  it('sem feedback: destaca só a selecionada', () => {
    expect(
      opcaoClassNames(styles, {}, { 10: 'B' }, 10, opcao),
    ).toBe(styles.optionOn);
  });

  it('sem feedback: opção não selecionada fica idle', () => {
    expect(
      opcaoClassNames(styles, {}, { 10: 'A' }, 10, opcao),
    ).toBe(styles.optionIdle);
  });

  it('com feedback: acerto mantém highlight', () => {
    const fb = { acertou: true, gabarito_opcao_id: 2 };
    expect(
      opcaoClassNames(styles, { 10: fb }, { 10: 'B' }, 10, opcao),
    ).toBe(styles.optionOn);
  });

  it('com feedback: erro revela gabarito e errada', () => {
    const fb = { acertou: false, gabarito_opcao_id: 2 };
    const errada = { id: 0, texto: 'A' };
    expect(
      opcaoClassNames(styles, { 10: fb }, { 10: 'A' }, 10, errada),
    ).toBe(`${styles.optionIdle} ${styles.optionWrongReveal}`);
    expect(
      opcaoClassNames(styles, { 10: fb }, { 10: 'A' }, 10, opcao),
    ).toBe(`${styles.optionIdle} ${styles.optionGabaritoReveal}`);
  });
});
