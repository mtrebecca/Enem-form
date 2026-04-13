import { useCallback, useEffect, useRef, useState } from 'react';
import { api, isAbortError } from '../api';
import { fetchQuestaoTreino } from '../lib/treinoApi';
import { useScopedAbort } from './useScopedAbort';

export function useTreinoFlow({ setLoading, setError }) {
  const nextFlowSignal = useScopedAbort();
  const answerAbortRef = useRef(null);

  useEffect(() => {
    return () => {
      answerAbortRef.current?.abort();
    };
  }, []);

  const [treinoDisciplinas, setTreinoDisciplinas] = useState([]);
  const [treinoFiltro, setTreinoFiltro] = useState('todas');
  const [treinoExcluir, setTreinoExcluir] = useState([]);
  const [treinoQuestao, setTreinoQuestao] = useState(null);
  const [treinoRespostas, setTreinoRespostas] = useState({});
  const [treinoFeedback, setTreinoFeedback] = useState({});

  const limparEstadoTreino = useCallback(() => {
    setTreinoDisciplinas([]);
    setTreinoFiltro('todas');
    setTreinoExcluir([]);
    setTreinoQuestao(null);
    setTreinoRespostas({});
    setTreinoFeedback({});
  }, []);

  const abrirTreino = useCallback(async () => {
    const signal = nextFlowSignal();
    setLoading(true);
    setError('');
    setTreinoExcluir([]);
    setTreinoRespostas({});
    setTreinoFeedback({});
    try {
      setTreinoDisciplinas(await api('/api/treino/disciplinas', { signal }));
      setTreinoFiltro('todas');
      setTreinoQuestao(await fetchQuestaoTreino([], 'todas', signal));
      return true;
    } catch (err) {
      if (isAbortError(err)) return false;
      setError(err.message);
      setTreinoQuestao(null);
      return false;
    } finally {
      setLoading(false);
    }
  }, [nextFlowSignal, setLoading, setError]);

  const aplicarFiltroTreino = useCallback(
    async (novoFiltro) => {
      const signal = nextFlowSignal();
      setTreinoFiltro(novoFiltro);
      setTreinoExcluir([]);
      setTreinoRespostas({});
      setTreinoFeedback({});
      setLoading(true);
      setError('');
      try {
        setTreinoQuestao(await fetchQuestaoTreino([], novoFiltro, signal));
      } catch (err) {
        if (isAbortError(err)) return;
        setError(err.message);
        setTreinoQuestao(null);
      } finally {
        setLoading(false);
      }
    },
    [nextFlowSignal, setLoading, setError],
  );

  const responderTreino = useCallback(
    async (questaoId, opcao) => {
      answerAbortRef.current?.abort();
      const ac = new AbortController();
      answerAbortRef.current = ac;
      setError('');
      setTreinoRespostas((prev) => ({ ...prev, [questaoId]: opcao.texto }));
      try {
        const data = await api('/api/treino/responder', {
          method: 'POST',
          body: JSON.stringify({ questao_id: questaoId, opcao_id: opcao.id }),
          signal: ac.signal,
        });
        if (data.feedback) {
          setTreinoFeedback((prev) => ({ ...prev, [questaoId]: data.feedback }));
        }
      } catch (err) {
        if (isAbortError(err)) return;
        setError(err.message);
      }
    },
    [setError],
  );

  const proximaQuestaoTreino = useCallback(async () => {
    if (!treinoQuestao) return;
    const signal = nextFlowSignal();
    const nextExcluir = [...treinoExcluir, treinoQuestao.id];
    setTreinoExcluir(nextExcluir);
    setTreinoRespostas({});
    setTreinoFeedback({});
    setLoading(true);
    setError('');
    try {
      setTreinoQuestao(await fetchQuestaoTreino(nextExcluir, treinoFiltro, signal));
    } catch (err) {
      if (isAbortError(err)) return;
      setError(err.message);
      setTreinoQuestao(null);
    } finally {
      setLoading(false);
    }
  }, [treinoQuestao, treinoExcluir, treinoFiltro, nextFlowSignal, setLoading, setError]);

  const reiniciarRodadaTreino = useCallback(async () => {
    const signal = nextFlowSignal();
    setTreinoExcluir([]);
    setTreinoRespostas({});
    setTreinoFeedback({});
    setLoading(true);
    setError('');
    try {
      setTreinoQuestao(await fetchQuestaoTreino([], treinoFiltro, signal));
    } catch (err) {
      if (isAbortError(err)) return;
      setError(err.message);
      setTreinoQuestao(null);
    } finally {
      setLoading(false);
    }
  }, [treinoFiltro, nextFlowSignal, setLoading, setError]);

  return {
    treinoDisciplinas,
    treinoFiltro,
    treinoQuestao,
    treinoRespostas,
    treinoFeedback,
    limparEstadoTreino,
    abrirTreino,
    aplicarFiltroTreino,
    responderTreino,
    proximaQuestaoTreino,
    reiniciarRodadaTreino,
  };
}
