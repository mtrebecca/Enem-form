import { useCallback, useEffect, useRef } from 'react';

/**
 * Cancela o pedido anterior ao iniciar outro e ao desmontar o componente.
 * Retorna uma função que gera um novo AbortSignal para passar a `api()`.
 */
export function useScopedAbort() {
  const controllerRef = useRef(null);

  useEffect(() => {
    return () => {
      controllerRef.current?.abort();
    };
  }, []);

  return useCallback(() => {
    controllerRef.current?.abort();
    const next = new AbortController();
    controllerRef.current = next;
    return next.signal;
  }, []);
}
