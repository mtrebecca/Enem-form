import { afterEach, describe, expect, it, vi } from 'vitest';
import { api, userIdStorage } from './api';

const KEY = 'enem_user_id';

describe('api()', () => {
  afterEach(() => {
    localStorage.removeItem(KEY);
    vi.unstubAllGlobals();
  });

  it('envia X-User-Id quando há id guardado', async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true }),
    });
    vi.stubGlobal('fetch', fetchMock);

    userIdStorage.set(42);
    await api('/api/dashboard');

    expect(fetchMock).toHaveBeenCalledWith(
      '/api/dashboard',
      expect.objectContaining({
        headers: expect.objectContaining({
          'Content-Type': 'application/json',
          'X-User-Id': '42',
        }),
      }),
    );
  });

  it('lança erro com mensagem do JSON quando response.ok é false', async () => {
    vi.stubGlobal(
      'fetch',
      vi.fn().mockResolvedValue({
        ok: false,
        json: async () => ({ message: 'Falhou' }),
      }),
    );

    await expect(api('/api/x')).rejects.toThrow('Falhou');
  });

  it('encaminha signal ao fetch', async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      json: async () => ({ ok: true }),
    });
    vi.stubGlobal('fetch', fetchMock);

    const ac = new AbortController();
    await api('/api/dashboard', { signal: ac.signal });

    expect(fetchMock).toHaveBeenCalledWith(
      '/api/dashboard',
      expect.objectContaining({ signal: ac.signal }),
    );
  });
});
