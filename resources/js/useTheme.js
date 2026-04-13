import { useEffect, useState } from 'react';

const KEY = 'enem_theme';

function readInitial() {
  if (typeof window === 'undefined') return 'light';
  const stored = localStorage.getItem(KEY);
  if (stored === 'dark' || stored === 'light') return stored;
  return 'light';
}

export function useTheme() {
  const [theme, setTheme] = useState(() => readInitial());

  useEffect(() => {
    localStorage.setItem(KEY, theme);
    document.documentElement.classList.toggle('dark', theme === 'dark');
  }, [theme]);

  return {
    theme,
    isDark: theme === 'dark',
    toggle: () => setTheme((t) => (t === 'dark' ? 'light' : 'dark')),
  };
}
