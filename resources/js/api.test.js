import { afterEach, describe, expect, it, vi } from 'vitest';
import { api, authTokenStorage } from './api';

const KEY = 'enem_auth_token';

describe('api()', () => {
  afterEach(() => {
    localStorage.removeItem(KEY);
    vi.unstubAllGlobals();
  });

  it('envia Authorization Bearer quando ha token guardado', async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 200,
      headers: { get: () => 'application/json' },
      json: async () => ({ ok: true }),
    });
    vi.stubGlobal('fetch', fetchMock);

    authTokenStorage.set('token-abc');
    await api('/api/dashboard');

    expect(fetchMock).toHaveBeenCalledWith(
      '/api/dashboard',
      expect.objectContaining({
        headers: expect.objectContaining({
          'Content-Type': 'application/json',
          Authorization: 'Bearer token-abc',
        }),
      }),
    );
  });

  it('lança erro com mensagem do JSON quando response.ok é false', async () => {
    vi.stubGlobal(
      'fetch',
      vi.fn().mockResolvedValue({
        ok: false,
        status: 401,
        headers: { get: () => 'application/json' },
        json: async () => ({ message: 'Falhou' }),
      }),
    );

    await expect(api('/api/x')).rejects.toThrow('Falhou');
  });

  it('encaminha signal ao fetch', async () => {
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 200,
      headers: { get: () => 'application/json' },
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

  it('retorna null para resposta 204 sem tentar parsear json', async () => {
    const jsonMock = vi.fn();
    const fetchMock = vi.fn().mockResolvedValue({
      ok: true,
      status: 204,
      headers: { get: () => null },
      json: jsonMock,
    });
    vi.stubGlobal('fetch', fetchMock);

    await expect(api('/api/auth/logout', { method: 'POST' })).resolves.toBeNull();
    expect(jsonMock).not.toHaveBeenCalled();
  });
});
