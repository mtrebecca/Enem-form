export function ui(isDark) {
  if (isDark) {
    return {
      shell: 'min-h-screen bg-slate-950 text-slate-100',
      card: 'rounded-2xl border border-slate-700/80 bg-slate-900/85 p-6 shadow-xl shadow-black/25 backdrop-blur-sm',
      innerCard: 'rounded-xl border border-slate-800 bg-slate-950/70 p-4',
      input:
        'w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 outline-none ring-2 ring-transparent focus:ring-sky-500',
      muted: 'text-slate-400',
      sub: 'text-slate-300',
      btnPrimary:
        'rounded-xl bg-gradient-to-r from-sky-600 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:from-sky-500 hover:to-emerald-500 disabled:cursor-not-allowed disabled:opacity-60',
      btnGhost:
        'rounded-xl border border-slate-600 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-slate-400 hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50',
      optionIdle: 'border border-slate-700 bg-slate-900 text-slate-200 hover:border-slate-500',
      optionOn: 'bg-gradient-to-r from-sky-600 to-emerald-600 text-white shadow-md',
      optionGabaritoReveal: 'ring-2 ring-emerald-400 ring-offset-2 ring-offset-slate-950 border-emerald-500/50',
      optionWrongReveal: 'ring-2 ring-rose-400 ring-offset-2 ring-offset-slate-950 border-rose-500/50 bg-rose-950/40',
      optionDimmed: 'opacity-45',
      feedbackOk: 'mt-3 rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-200',
      feedbackBad: 'mt-3 rounded-lg border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-sm font-medium text-rose-200',
      error: 'rounded-xl border border-red-500/40 bg-red-500/10 p-3 text-sm text-red-200',
      success: 'rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-3 text-sm text-emerald-200',
      histItem: 'rounded-lg border border-slate-800 px-3 py-2 text-xs text-slate-300',
      histMeta: 'text-slate-400',
      histSmall: 'text-slate-500',
      linkMuted: 'text-slate-400 hover:text-slate-100',
      pagerBar: 'mt-4 flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/70 px-4 py-3 text-sm',
      tagline: 'text-[10px] font-semibold uppercase tracking-[0.2em] text-sky-300/80',
      disciplineOk: 'text-sm text-emerald-300',
      disciplineBad: 'text-sm text-rose-300',
      toggle:
        'fixed bottom-6 right-6 z-[100] flex items-center gap-2 rounded-full border border-slate-600 bg-slate-900/95 px-4 py-2.5 text-sm font-medium text-slate-100 shadow-lg backdrop-blur-md transition hover:border-sky-500/50 hover:bg-slate-800',
    };
  }

  return {
    shell: 'min-h-screen bg-gradient-to-br from-sky-50 via-white to-emerald-50 text-slate-900',
    card: 'rounded-2xl border border-sky-100/80 bg-white/90 p-6 shadow-lg shadow-sky-200/35 backdrop-blur-sm',
    innerCard: 'rounded-xl border border-sky-100 bg-white/95 p-4 shadow-sm',
    input:
      'w-full rounded-xl border border-sky-200 bg-white px-3 py-2 text-sm text-slate-900 outline-none ring-2 ring-transparent focus:ring-sky-500',
    muted: 'text-slate-600',
    sub: 'text-slate-700',
    btnPrimary:
      'rounded-xl bg-gradient-to-r from-sky-600 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:from-sky-700 hover:to-emerald-700 disabled:cursor-not-allowed disabled:opacity-60',
    btnGhost:
      'rounded-xl border border-sky-200 bg-white/80 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:border-sky-300 hover:bg-white disabled:cursor-not-allowed disabled:opacity-50',
    optionIdle: 'border border-sky-200 bg-white text-slate-800 hover:border-sky-400',
    optionOn: 'bg-gradient-to-r from-sky-600 to-emerald-600 text-white shadow-md',
    optionGabaritoReveal: 'ring-2 ring-emerald-600 ring-offset-2 ring-offset-white border-emerald-300 bg-emerald-50/80',
    optionWrongReveal: 'ring-2 ring-rose-500 ring-offset-2 ring-offset-white border-rose-200 bg-rose-50',
    optionDimmed: 'opacity-45',
    feedbackOk: 'mt-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-900',
    feedbackBad: 'mt-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-900',
    error: 'rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800',
    success: 'rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900',
    histItem: 'rounded-lg border border-sky-100 bg-white/90 px-3 py-2 text-xs text-slate-800',
    histMeta: 'text-slate-600',
    histSmall: 'text-slate-500',
    linkMuted: 'text-slate-600 hover:text-sky-700',
    pagerBar: 'mt-4 flex items-center justify-between rounded-xl border border-sky-100 bg-white/90 px-4 py-3 text-sm shadow-sm',
    tagline: 'text-[10px] font-semibold uppercase tracking-[0.2em] text-sky-600/90',
    disciplineOk: 'text-sm text-emerald-700',
    disciplineBad: 'text-sm text-rose-600',
    toggle:
      'fixed bottom-6 right-6 z-[100] flex items-center gap-2 rounded-full border border-sky-200 bg-white/95 px-4 py-2.5 text-sm font-medium text-slate-800 shadow-lg backdrop-blur-md transition hover:border-sky-400 hover:shadow-sky-200/50',
  };
}
