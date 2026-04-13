const USER_ID_KEY = 'enem_user_id';

export const userIdStorage = {
  get: () => localStorage.getItem(USER_ID_KEY),
  set: (id) => localStorage.setItem(USER_ID_KEY, String(id)),
  clear: () => localStorage.removeItem(USER_ID_KEY),
};

export function isAbortError(err) {
  return err != null && err.name === 'AbortError';
}

export async function api(url, options = {}) {
  const { signal, headers: hdr = {}, ...rest } = options;
  const headers = { 'Content-Type': 'application/json', ...hdr };
  const uid = userIdStorage.get();
  if (uid) headers['X-User-Id'] = uid;

  const response = await fetch(url, { ...rest, headers, signal });

  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || 'Erro inesperado na requisicao.');
  }
  return data;
}
