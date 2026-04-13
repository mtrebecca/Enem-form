import React, { useCallback, useMemo, useState } from 'react';
import { api, isAbortError, userIdStorage } from '../api';
import { useScopedAbort } from '../hooks/useScopedAbort';
import BrandMark from '../components/BrandMark';
import ThemeToggle from '../components/ThemeToggle';
import { useProvaFlow } from '../hooks/useProvaFlow';
import { useTreinoFlow } from '../hooks/useTreinoFlow';
import AuthScreen from '../screens/AuthScreen';
import DashboardScreen from '../screens/DashboardScreen';
import ProvaScreen from '../screens/ProvaScreen';
import ResultadoScreen from '../screens/ResultadoScreen';
import TreinoScreen from '../screens/TreinoScreen';
import { useTheme } from '../useTheme';
import { ui } from '../uiStyles';

const initialAuthForm = { nome: '', email: '', senha: '' };

export default function App() {
  const { isDark, toggle: toggleTheme } = useTheme();
  const s = useMemo(() => ui(isDark), [isDark]);

  const [screen, setScreen] = useState('auth');
  const [authMode, setAuthMode] = useState('login');
  const [authForm, setAuthForm] = useState(initialAuthForm);
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const [provas, setProvas] = useState([]);
  const [historico, setHistorico] = useState([]);

  const nextAppSignal = useScopedAbort();

  const refreshDashboard = useCallback(async (signal) => {
    const opts = signal ? { signal } : {};
    const bundle = await api('/api/dashboard', opts);
    setProvas(bundle.provas);
    setHistorico(bundle.historico);
  }, []);

  const provaFlow = useProvaFlow({ setLoading, setError, refreshDashboard });
  const treinoFlow = useTreinoFlow({ setLoading, setError });

  const acertosTotais = useMemo(() => {
    const resultado = provaFlow.resultado;
    if (resultado?.totais?.total_acertos != null) {
      return resultado.totais.total_acertos;
    }
    if (!resultado?.disciplinas) return 0;
    return Object.values(resultado.disciplinas).reduce((sum, item) => sum + item.acertos, 0);
  }, [provaFlow.resultado]);

  const entrarNoPainel = async (usuario, signal) => {
    setUser(usuario);
    userIdStorage.set(usuario.id);
    await refreshDashboard(signal);
    setScreen('dashboard');
  };

  const handleAuthSubmit = async (event) => {
    event.preventDefault();
    const signal = nextAppSignal();
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
        setSuccess(data.message);
        await entrarNoPainel(data.user, signal);
      } else {
        const data = await api('/api/auth/login', {
          method: 'POST',
          body: JSON.stringify({ email: authForm.email, senha: authForm.senha }),
          signal,
        });
        await entrarNoPainel(data.user, signal);
      }
    } catch (err) {
      if (!isAbortError(err)) setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const logout = async () => {
    await api('/api/auth/logout', { method: 'POST' });
    userIdStorage.clear();
    setUser(null);
    setScreen('auth');
    setAuthForm(initialAuthForm);
    provaFlow.limparEstadoProva();
    treinoFlow.limparEstadoTreino();
    setError('');
    setSuccess('');
  };

  const handleAbrirTreino = async () => {
    const ok = await treinoFlow.abrirTreino();
    if (ok) setScreen('treino');
  };

  const handleIniciarProva = async (id) => {
    const ok = await provaFlow.iniciarProva(id);
    if (ok) setScreen('prova');
  };

  const handleFinalizarProva = async () => {
    const ok = await provaFlow.finalizarProva();
    if (ok) setScreen('resultado');
  };

  const handleForgotPassword = async () => {
    const signal = nextAppSignal();
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
  };

  return (
    <div className={s.shell}>
      <main className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 pb-24 sm:px-6">
        <header className="flex flex-wrap items-center justify-between gap-4">
          <BrandMark isDark={isDark} />
          {user && (
            <button type="button" onClick={logout} className={s.btnGhost}>
              Sair
            </button>
          )}
        </header>

        {error && <div className={s.error}>{error}</div>}
        {success && <div className={s.success}>{success}</div>}

        {screen === 'auth' && (
          <AuthScreen
            s={s}
            authMode={authMode}
            onToggleAuthMode={() => {
              setAuthMode((m) => (m === 'login' ? 'register' : 'login'));
              setError('');
              setSuccess('');
            }}
            authForm={authForm}
            setAuthForm={setAuthForm}
            loading={loading}
            handleAuthSubmit={handleAuthSubmit}
            onForgotPassword={handleForgotPassword}
          />
        )}

        {screen === 'dashboard' && (
          <DashboardScreen
            s={s}
            provas={provas}
            historico={historico}
            user={user}
            loading={loading}
            onTreino={handleAbrirTreino}
            onIniciarProva={handleIniciarProva}
          />
        )}

        {screen === 'treino' && (
          <TreinoScreen
            s={s}
            loading={loading}
            treinoDisciplinas={treinoFlow.treinoDisciplinas}
            treinoFiltro={treinoFlow.treinoFiltro}
            treinoQuestao={treinoFlow.treinoQuestao}
            treinoRespostas={treinoFlow.treinoRespostas}
            treinoFeedback={treinoFlow.treinoFeedback}
            onBackDashboard={() => setScreen('dashboard')}
            aplicarFiltroTreino={treinoFlow.aplicarFiltroTreino}
            reiniciarRodadaTreino={treinoFlow.reiniciarRodadaTreino}
            responderTreino={treinoFlow.responderTreino}
            proximaQuestaoTreino={treinoFlow.proximaQuestaoTreino}
          />
        )}

        {screen === 'prova' && provaFlow.provaAtual && (
          <ProvaScreen
            s={s}
            provaAtual={provaFlow.provaAtual}
            questoes={provaFlow.questoes}
            questoesMeta={provaFlow.questoesMeta}
            loading={loading}
            feedbackPorQuestao={provaFlow.feedbackPorQuestao}
            respostas={provaFlow.respostas}
            onBackDashboard={() => setScreen('dashboard')}
            responder={provaFlow.responder}
            carregarPaginaQuestoes={provaFlow.carregarPaginaQuestoes}
            finalizarProva={handleFinalizarProva}
          />
        )}

        {screen === 'resultado' && provaFlow.resultado && (
          <ResultadoScreen
            s={s}
            resultado={provaFlow.resultado}
            acertosTotais={acertosTotais}
            onVoltarDashboard={() => setScreen('dashboard')}
            onLimparSessao={() => {
              provaFlow.setResultado(null);
              provaFlow.limparEstadoProva();
            }}
          />
        )}
      </main>

      <ThemeToggle isDark={isDark} onToggle={toggleTheme} toggleClass={s.toggle} />
    </div>
  );
}
