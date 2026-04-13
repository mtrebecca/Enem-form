import { render } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import BrandMark from '../../components/BrandMark';

function findParagrafo(container, texto) {
  return [...container.querySelectorAll('p')].find((p) => p.textContent === texto);
}

describe('BrandMark', () => {
  it('mostra o título da marca', () => {
    const { container } = render(<BrandMark isDark={false} />);
    expect(findParagrafo(container, 'ENEM.pratica')).toBeTruthy();
  });

  it('mostra o subtítulo', () => {
    const { container } = render(<BrandMark isDark />);
    expect(findParagrafo(container, 'preparacao enem')).toBeTruthy();
  });
});
