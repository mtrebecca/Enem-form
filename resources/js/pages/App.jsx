import React, { useCallback, useMemo, useState } from 'react';
import { api } from '../api';
import BrandMark from '../components/BrandMark';
import ThemeToggle from '../components/ThemeToggle';
import { useAuthFlow } from '../hooks/useAuthFlow';
import { useNavigation } from '../hooks/useNavigation';
import { useProvaFlow } from '../hooks/useProvaFlow';
import { useTreinoFlow } from '../hooks/useTreinoFlow';
import AuthScreen from '../screens/AuthScreen';
import DashboardScreen from '../screens/DashboardScreen';
import ProvaScreen from '../screens/ProvaScreen';
import ResultadoScreen from '../screens/ResultadoScreen';
import TreinoScreen from '../screens/TreinoScreen';
import { useTheme } from '../useTheme';
import { ui } from '../uiStyles';

export default function App() {
  const { isDark, toggle: toggleTheme } = useTheme();
  const s = useMemo(() => ui(isDark), [isDark]);

  const navigation = useNavigation('auth');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const [provas, setProvas] = useState([]);
  const [historico, setHistorico] = useState([]);

  const refreshDashboard = useCallback(async (signal) => {
    const bundle = await api('/api/dashboard', signal ? { signal } : {});
    setProvas(bundle.provas);
    setHistorico(bundle.historico);
  }, []);

  const provaFlow = useProvaFlow({ setLoading, setError, refreshDashboard });
  const treinoFlow = useTreinoFlow({ setLoading, setError });
  const authFlow = useAuthFlow({
    setLoading,
    setError,
    setSuccess,
    onLoginSuccess: async (_usuario, signal) => {
      await refreshDashboard(signal);
      navigation.goDashboard();
    },
    onLogoutCleanup: () => {
      provaFlow.limparEstadoProva();
      treinoFlow.limparEstadoTreino();
      navigation.goAuth();
    },
  });

  const acertosTotais = useMemo(() => {
    const resultado = provaFlow.resultado;
    if (resultado?.totais?.total_acertos != null) {
      return resultado.totais.total_acertos;
    }
    if (!resultado?.disciplinas) return 0;
    return Object.values(resultado.disciplinas).reduce((sum, item) => sum + item.acertos, 0);
  }, [provaFlow.resultado]);

  const handleAbrirTreino = async () => {
    const ok = await treinoFlow.abrirTreino();
    if (ok) navigation.goTreino();
  };

  const handleIniciarProva = async (id) => {
    const ok = await provaFlow.iniciarProva(id);
    if (ok) navigation.goProva();
  };

  const handleFinalizarProva = async () => {
    const ok = await provaFlow.finalizarProva();
    if (ok) navigation.goResultado();
  };

  return (
    <div className={s.shell}>
      <main className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 pb-24 sm:px-6">
        <header className="relative flex min-h-12 items-center justify-center">
          <BrandMark isDark={isDark} />
          {authFlow.user && (
            <button type="button" onClick={authFlow.logout} className={`${s.btnGhost} absolute right-0`}>
              Sair
            </button>
          )}
        </header>

        {error && <div className={s.error}>{error}</div>}
        {success && <div className={s.success}>{success}</div>}

        {navigation.screen === 'auth' && (
          <AuthScreen
            s={s}
            authMode={authFlow.authMode}
            onToggleAuthMode={authFlow.toggleAuthMode}
            authForm={authFlow.authForm}
            setAuthForm={authFlow.setAuthForm}
            loading={loading}
            handleAuthSubmit={authFlow.handleAuthSubmit}
            onForgotPassword={authFlow.handleForgotPassword}
          />
        )}

        {navigation.screen === 'dashboard' && (
          <DashboardScreen
            s={s}
            provas={provas}
            historico={historico}
            user={authFlow.user}
            loading={loading}
            onTreino={handleAbrirTreino}
            onIniciarProva={handleIniciarProva}
          />
        )}

        {navigation.screen === 'treino' && (
          <TreinoScreen
            s={s}
            loading={loading}
            flow={treinoFlow}
            onBackDashboard={navigation.goDashboard}
          />
        )}

        {navigation.screen === 'prova' && provaFlow.provaAtual && (
          <ProvaScreen
            s={s}
            loading={loading}
            flow={provaFlow}
            onBackDashboard={navigation.goDashboard}
            onFinalizarProva={handleFinalizarProva}
          />
        )}

        {navigation.screen === 'resultado' && provaFlow.resultado && (
          <ResultadoScreen
            s={s}
            resultado={provaFlow.resultado}
            acertosTotais={acertosTotais}
            onVoltarDashboard={navigation.goDashboard}
          />
        )}
      </main>

      <ThemeToggle isDark={isDark} onToggle={toggleTheme} toggleClass={s.toggle} />
    </div>
  );
}
