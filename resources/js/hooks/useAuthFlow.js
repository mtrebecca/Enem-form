import { useCallback, useState } from 'react';
import { api, authTokenStorage, isAbortError } from '../api';
import { useScopedAbort } from './useScopedAbort';

const initialAuthForm = { nome: '', email: '', senha: '' };

export function useAuthFlow({ setLoading, setError, setSuccess, onLoginSuccess, onLogoutCleanup }) {
  const nextSignal = useScopedAbort();
  const [authMode, setAuthMode] = useState('login');
  const [authForm, setAuthForm] = useState(initialAuthForm);
  const [user, setUser] = useState(null);

  const toggleAuthMode = useCallback(() => {
    setAuthMode((m) => (m === 'login' ? 'register' : 'login'));
    setError('');
    setSuccess('');
  }, [setError, setSuccess]);

  const handleAuthSubmit = useCallback(
    async (event) => {
      event.preventDefault();
      const signal = nextSignal();
      setLoading(true);
      setError('');
      setSuccess('');

      try {
        if (authMode === 'register') {
          const data = await api('/api/auth/register', {
            method: 'POST',
            body: JSON.stringify({
              nome: authForm.nome,
              email: authForm.email,
              senha: authForm.senha,
            }),
            signal,
          });
          authTokenStorage.set(data.token);
          setSuccess(data.message);
          setUser(data.user);
          await onLoginSuccess(data.user, signal);
          return;
        }

        const data = await api('/api/auth/login', {
          method: 'POST',
          body: JSON.stringify({ email: authForm.email, senha: authForm.senha }),
          signal,
        });
        authTokenStorage.set(data.token);
        setUser(data.user);
        await onLoginSuccess(data.user, signal);
      } catch (err) {
        if (!isAbortError(err)) setError(err.message);
      } finally {
        setLoading(false);
      }
    },
    [authMode, authForm, nextSignal, onLoginSuccess, setError, setLoading, setSuccess],
  );

  const handleForgotPassword = useCallback(async () => {
    const signal = nextSignal();
    try {
      if (!authForm.email) {
        setError('Informe o email para recuperar a senha.');
        return;
      }
      const data = await api('/api/auth/esqueci-senha', {
        method: 'POST',
        body: JSON.stringify({ email: authForm.email }),
        signal,
      });
      setSuccess(data.message);
    } catch (err) {
      if (!isAbortError(err)) setError(err.message);
    }
  }, [authForm.email, nextSignal, setError, setSuccess]);

  const logout = useCallback(async () => {
    await api('/api/auth/logout', { method: 'POST' });
    authTokenStorage.clear();
    setUser(null);
    setAuthForm(initialAuthForm);
    setError('');
    setSuccess('');
    onLogoutCleanup();
  }, [onLogoutCleanup, setError, setSuccess]);

  return {
    authMode,
    authForm,
    user,
    setAuthForm,
    toggleAuthMode,
    handleAuthSubmit,
    handleForgotPassword,
    logout,
  };
}
