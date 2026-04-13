import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import ThemeToggle from '../../components/ThemeToggle';

describe('ThemeToggle', () => {
  it('chama onToggle ao clicar', async () => {
    const user = userEvent.setup();
    const onToggle = vi.fn();

    render(<ThemeToggle isDark={false} onToggle={onToggle} toggleClass="rounded px-2" />);

    await user.click(screen.getByRole('button', { name: /modo escuro/i }));
    expect(onToggle).toHaveBeenCalledTimes(1);
  });

  it('em modo escuro, o rótulo convida a ativar modo claro', () => {
    render(<ThemeToggle isDark onToggle={() => {}} toggleClass="" />);
    expect(screen.getByRole('button', { name: /modo claro/i })).toBeInTheDocument();
  });
});
