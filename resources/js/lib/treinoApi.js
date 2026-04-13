import { api } from '../api';

export function fetchQuestaoTreino(excluirIds, filtroDisciplina, signal) {
  const params = new URLSearchParams();
  if (filtroDisciplina && filtroDisciplina !== 'todas') {
    params.set('disciplina', filtroDisciplina);
  }
  excluirIds.forEach((id) => params.append('excluir[]', String(id)));
  const qs = params.toString();
  const opts = signal ? { signal } : {};
  return api(`/api/treino/questao-aleatoria${qs ? `?${qs}` : ''}`, opts);
}
