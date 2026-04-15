import { useCallback, useEffect, useRef, useState } from 'react';
import { api, isAbortError } from '../api';
import { fetchQuestaoTreino } from '../lib/treinoApi';
import { useScopedAbort } from './useScopedAbort';

function isSemQuestoesError(err) {
  const message = (err?.message ?? '').toLowerCase();
  return message.includes('nao ha questoes disponiveis');
}

export function useTreinoFlow({ setLoading, setError }) {
  const nextFlowSignal = useScopedAbort();
  const answerAbortRef = useRef(null);
  const treinoRespondidasRef = useRef(new Set());
  const treinoAcertoPorQuestaoRef = useRef(new Map());

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
  const [treinoResumoRodada, setTreinoResumoRodada] = useState({ respondidas: 0, acertos: 0 });
  const [treinoRodadaFinalizada, setTreinoRodadaFinalizada] = useState(false);
  const [treinoEhUltimaQuestao, setTreinoEhUltimaQuestao] = useState(false);

  const limparRespostaAtual = useCallback(() => {
    setTreinoRespostas({});
    setTreinoFeedback({});
  }, []);

  const resetRodadaTreino = useCallback(
    (filtro = 'todas') => {
      setTreinoFiltro(filtro);
      setTreinoExcluir([]);
      setTreinoQuestao(null);
      limparRespostaAtual();
      treinoRespondidasRef.current = new Set();
      treinoAcertoPorQuestaoRef.current = new Map();
      setTreinoResumoRodada({ respondidas: 0, acertos: 0 });
      setTreinoRodadaFinalizada(false);
      setTreinoEhUltimaQuestao(false);
    },
    [limparRespostaAtual],
  );

  const atualizarEstadoUltimaQuestao = useCallback(
    async (questaoAtual, excluirAtual, filtroAtual, signal) => {
      if (!questaoAtual?.id) {
        setTreinoEhUltimaQuestao(false);
        return;
      }

      try {
        await fetchQuestaoTreino([...excluirAtual, questaoAtual.id], filtroAtual, signal);
        setTreinoEhUltimaQuestao(false);
      } catch (err) {
        if (isAbortError(err)) return;
        if (isSemQuestoesError(err)) {
          setTreinoEhUltimaQuestao(true);
          return;
        }
        throw err;
      }
    },
    [],
  );

  const limparEstadoTreino = useCallback(() => {
    setTreinoDisciplinas([]);
    resetRodadaTreino('todas');
  }, [resetRodadaTreino]);

  const abrirTreino = useCallback(async () => {
    const signal = nextFlowSignal();
    setLoading(true);
    setError('');
    resetRodadaTreino('todas');
    try {
      setTreinoDisciplinas(await api('/api/treino/disciplinas', { signal }));
      const questaoInicial = await fetchQuestaoTreino([], 'todas', signal);
      setTreinoQuestao(questaoInicial);
      await atualizarEstadoUltimaQuestao(questaoInicial, [], 'todas', signal);
      return true;
    } catch (err) {
      if (isAbortError(err)) return false;
      if (isSemQuestoesError(err)) {
        setError('');
        setTreinoQuestao(null);
        setTreinoEhUltimaQuestao(false);
        return true;
      }
      setError(err.message);
      setTreinoQuestao(null);
      return false;
    } finally {
      setLoading(false);
    }
  }, [nextFlowSignal, resetRodadaTreino, setLoading, setError, atualizarEstadoUltimaQuestao]);

  const aplicarFiltroTreino = useCallback(
    async (novoFiltro) => {
      const signal = nextFlowSignal();
      resetRodadaTreino(novoFiltro);
      setLoading(true);
      setError('');
      try {
        const questaoInicial = await fetchQuestaoTreino([], novoFiltro, signal);
        setTreinoQuestao(questaoInicial);
        await atualizarEstadoUltimaQuestao(questaoInicial, [], novoFiltro, signal);
      } catch (err) {
        if (isAbortError(err)) return;
        if (isSemQuestoesError(err)) {
          setError('');
          setTreinoQuestao(null);
          setTreinoEhUltimaQuestao(false);
          return;
        }
        setError(err.message);
        setTreinoQuestao(null);
      } finally {
        setLoading(false);
      }
    },
    [nextFlowSignal, setLoading, setError, atualizarEstadoUltimaQuestao, resetRodadaTreino],
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
          const acertouAtual = Boolean(data.feedback.acertou);
          const acertouAnterior = treinoAcertoPorQuestaoRef.current.get(questaoId);

          if (!treinoRespondidasRef.current.has(questaoId)) {
            treinoRespondidasRef.current.add(questaoId);
            treinoAcertoPorQuestaoRef.current.set(questaoId, acertouAtual);
            setTreinoResumoRodada((prev) => ({
              respondidas: prev.respondidas + 1,
              acertos: prev.acertos + (acertouAtual ? 1 : 0),
            }));
            return;
          }

          if (acertouAnterior === acertouAtual) {
            return;
          }

          treinoAcertoPorQuestaoRef.current.set(questaoId, acertouAtual);
          setTreinoResumoRodada((prev) => ({
            ...prev,
            acertos: prev.acertos + (acertouAtual ? 1 : -1),
          }));
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
    limparRespostaAtual();
    setTreinoRodadaFinalizada(false);
    setTreinoEhUltimaQuestao(false);
    setLoading(true);
    setError('');
    try {
      const proxima = await fetchQuestaoTreino(nextExcluir, treinoFiltro, signal);
      setTreinoQuestao(proxima);
      await atualizarEstadoUltimaQuestao(proxima, nextExcluir, treinoFiltro, signal);
    } catch (err) {
      if (isAbortError(err)) return;
      if (isSemQuestoesError(err)) {
        setError('');
        setTreinoQuestao(null);
        setTreinoEhUltimaQuestao(false);
        return;
      }
      setError(err.message);
      setTreinoQuestao(null);
    } finally {
      setLoading(false);
    }
  }, [treinoQuestao, treinoExcluir, treinoFiltro, nextFlowSignal, setLoading, setError, atualizarEstadoUltimaQuestao, limparRespostaAtual]);

  const reiniciarRodadaTreino = useCallback(async () => {
    const signal = nextFlowSignal();
    resetRodadaTreino(treinoFiltro);
    setLoading(true);
    setError('');
    try {
      const questaoInicial = await fetchQuestaoTreino([], treinoFiltro, signal);
      setTreinoQuestao(questaoInicial);
      await atualizarEstadoUltimaQuestao(questaoInicial, [], treinoFiltro, signal);
    } catch (err) {
      if (isAbortError(err)) return;
      if (isSemQuestoesError(err)) {
        setError('');
        setTreinoQuestao(null);
        setTreinoEhUltimaQuestao(false);
        return;
      }
      setError(err.message);
      setTreinoQuestao(null);
    } finally {
      setLoading(false);
    }
  }, [treinoFiltro, nextFlowSignal, setLoading, setError, atualizarEstadoUltimaQuestao, resetRodadaTreino]);

  const finalizarRodadaTreino = useCallback(() => {
    if (treinoResumoRodada.respondidas < 1) {
      setError('Responda pelo menos uma questao antes de finalizar a rodada.');
      return;
    }
    setError('');
    setTreinoRodadaFinalizada(true);
    setTreinoQuestao(null);
    limparRespostaAtual();
    setTreinoEhUltimaQuestao(false);
  }, [treinoResumoRodada.respondidas, setError, limparRespostaAtual]);

  return {
    treinoDisciplinas,
    treinoFiltro,
    treinoQuestao,
    treinoRespostas,
    treinoFeedback,
    treinoResumoRodada,
    treinoRodadaFinalizada,
    treinoEhUltimaQuestao,
    limparEstadoTreino,
    abrirTreino,
    aplicarFiltroTreino,
    responderTreino,
    proximaQuestaoTreino,
    reiniciarRodadaTreino,
    finalizarRodadaTreino,
  };
}
