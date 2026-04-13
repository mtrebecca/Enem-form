import React from 'react';

export default function AuthScreen({
  s,
  authMode,
  onToggleAuthMode,
  authForm,
  setAuthForm,
  loading,
  handleAuthSubmit,
  onForgotPassword,
}) {
  return (
    <section className={`${s.card} mx-auto w-full max-w-md`}>
      <p className={`mb-1 ${s.tagline}`}>Bem-vindo</p>
      <h2 className="mb-4 text-xl font-semibold">{authMode === 'login' ? 'Entrar' : 'Criar conta'}</h2>

      <form onSubmit={handleAuthSubmit} className="space-y-3">
        {authMode === 'register' && (
          <input
            className={s.input}
            placeholder="Nome completo"
            value={authForm.nome}
            onChange={(e) => setAuthForm((p) => ({ ...p, nome: e.target.value }))}
            required
          />
        )}
        <input
          type="email"
          className={s.input}
          placeholder="Email"
          value={authForm.email}
          onChange={(e) => setAuthForm((p) => ({ ...p, email: e.target.value }))}
          required
        />
        <input
          type="password"
          className={s.input}
          placeholder="Senha"
          value={authForm.senha}
          onChange={(e) => setAuthForm((p) => ({ ...p, senha: e.target.value }))}
          required
        />
        <button type="submit" disabled={loading} className={`${s.btnPrimary} w-full`}>
          {loading ? 'Processando...' : authMode === 'login' ? 'Entrar' : 'Cadastrar'}
        </button>
      </form>

      <div className="mt-4 flex justify-between text-sm">
        <button type="button" className={s.linkMuted} onClick={onToggleAuthMode}>
          {authMode === 'login' ? 'Criar conta' : 'Ja tenho conta'}
        </button>
        <button type="button" className={s.linkMuted} onClick={onForgotPassword}>
          Esqueci senha
        </button>
      </div>
    </section>
  );
}
