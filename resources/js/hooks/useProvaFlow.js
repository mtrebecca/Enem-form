import { useCallback, useEffect, useRef, useState } from 'react';
import { api, isAbortError } from '../api';
import { META_QUESTOES_INICIAL, QUESTOES_POR_PAGINA } from '../constants';
import { useScopedAbort } from './useScopedAbort';

export function useProvaFlow({ setLoading, setError, refreshDashboard }) {
  const nextFlowSignal = useScopedAbort();
  const answerAbortRef = useRef(null);

  useEffect(() => {
    return () => {
      answerAbortRef.current?.abort();
    };
  }, []);

  const [provaAtual, setProvaAtual] = useState(null);
  const [questoes, setQuestoes] = useState([]);
  const [questoesMeta, setQuestoesMeta] = useState(META_QUESTOES_INICIAL);
  const [respostas, setRespostas] = useState({});
  const [feedbackPorQuestao, setFeedbackPorQuestao] = useState({});
  const [resultado, setResultado] = useState(null);

  const limparInteracoesProva = useCallback(() => {
    setRespostas({});
    setFeedbackPorQuestao({});
  }, []);

  const limparEstadoProva = useCallback(() => {
    setProvaAtual(null);
    setQuestoes([]);
    setQuestoesMeta(META_QUESTOES_INICIAL);
    limparInteracoesProva();
    setResultado(null);
  }, [limparInteracoesProva]);

  const iniciarProva = useCallback(
    async (id) => {
      const signal = nextFlowSignal();
      setLoading(true);
      setError('');
      try {
        const inicio = await api(
          `/api/provas/${id}/iniciar?expand=prova,questoes&per_page=${QUESTOES_POR_PAGINA}`,
          { method: 'POST', signal },
        );
        setProvaAtual(inicio.prova);
        setQuestoes(inicio.questoes.data);
        setQuestoesMeta(inicio.questoes.meta);
        limparInteracoesProva();
        setResultado(null);
        return true;
      } catch (err) {
        if (isAbortError(err)) return false;
        setError(err.message);
        return false;
      } finally {
        setLoading(false);
      }
    },
    [nextFlowSignal, setLoading, setError, limparInteracoesProva],
  );

  const responder = useCallback(
    async (questaoId, opcao) => {
      if (!provaAtual) return;
      answerAbortRef.current?.abort();
      const ac = new AbortController();
      answerAbortRef.current = ac;
      setError('');
      setRespostas((prev) => ({ ...prev, [questaoId]: opcao.texto }));
      try {
        const data = await api(`/api/provas/${provaAtual.id}/questoes/${questaoId}/resposta`, {
          method: 'PUT',
          body: JSON.stringify({ opcao_id: opcao.id }),
          signal: ac.signal,
        });
        if (data.feedback) {
          setFeedbackPorQuestao((prev) => ({ ...prev, [questaoId]: data.feedback }));
        }
      } catch (err) {
        if (isAbortError(err)) return;
        setError(err.message);
      }
    },
    [provaAtual, setError],
  );

  const carregarPaginaQuestoes = useCallback(
    async (nextPage) => {
      if (!provaAtual) return;
      const signal = nextFlowSignal();
      setLoading(true);
      setError('');
      try {
        const questoesData = await api(
          `/api/provas/${provaAtual.id}/questoes?page=${nextPage}&per_page=${QUESTOES_POR_PAGINA}`,
          { signal },
        );
        setQuestoes(questoesData.data);
        setQuestoesMeta(questoesData.meta);
      } catch (err) {
        if (isAbortError(err)) return;
        setError(err.message);
      } finally {
        setLoading(false);
      }
    },
    [provaAtual, nextFlowSignal, setLoading, setError],
  );

  const finalizarProva = useCallback(async () => {
    if (!provaAtual) return;
    const signal = nextFlowSignal();
    setLoading(true);
    setError('');
    try {
      await api(`/api/provas/${provaAtual.id}/finalizar`, { method: 'POST', signal });
      const resultadoData = await api(`/api/resultados/${provaAtual.id}`, { signal });
      setResultado(resultadoData);
      await refreshDashboard(signal);
      return true;
    } catch (err) {
      if (isAbortError(err)) return false;
      setError(err.message);
      return false;
    } finally {
      setLoading(false);
    }
  }, [provaAtual, nextFlowSignal, setLoading, setError, refreshDashboard]);

  return {
    provaAtual,
    questoes,
    questoesMeta,
    respostas,
    feedbackPorQuestao,
    resultado,
    setResultado,
    limparEstadoProva,
    iniciarProva,
    responder,
    carregarPaginaQuestoes,
    finalizarProva,
  };
}
