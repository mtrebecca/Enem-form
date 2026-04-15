const AUTH_TOKEN_KEY = 'enem_auth_token';

export const authTokenStorage = {
  get: () => localStorage.getItem(AUTH_TOKEN_KEY),
  set: (token) => localStorage.setItem(AUTH_TOKEN_KEY, String(token)),
  clear: () => localStorage.removeItem(AUTH_TOKEN_KEY),
};

export function isAbortError(err) {
  return err != null && err.name === 'AbortError';
}

export async function api(url, options = {}) {
  const { signal, headers: hdr = {}, ...rest } = options;
  const headers = { 'Content-Type': 'application/json', ...hdr };
  const token = authTokenStorage.get();
  if (token) headers.Authorization = `Bearer ${token}`;

  const response = await fetch(url, { ...rest, headers, signal });
  if (response.status === 204) {
    return null;
  }
  const contentType = response.headers.get('content-type') ?? '';
  const hasJsonBody = contentType.includes('application/json');
  const data = hasJsonBody ? await response.json() : null;
  if (!response.ok) {
    throw new Error(data?.message || 'Erro inesperado na requisicao.');
  }
  return data ?? {};
}
