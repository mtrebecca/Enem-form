import { useCallback, useState } from 'react';

export function useNavigation(initialScreen = 'auth') {
  const [screen, setScreen] = useState(initialScreen);

  const goTo = useCallback((nextScreen) => setScreen(nextScreen), []);
  const goAuth = useCallback(() => setScreen('auth'), []);
  const goDashboard = useCallback(() => setScreen('dashboard'), []);
  const goProva = useCallback(() => setScreen('prova'), []);
  const goTreino = useCallback(() => setScreen('treino'), []);
  const goResultado = useCallback(() => setScreen('resultado'), []);

  return { screen, setScreen, goTo, goAuth, goDashboard, goProva, goTreino, goResultado };
}
