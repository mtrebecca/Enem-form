export default function BrandMark({ isDark }) {
  const ring = isDark ? 'from-sky-400 to-emerald-400' : 'from-sky-500 to-emerald-500';
  const titleMain = isDark ? 'text-white' : 'text-sky-900';
  const titleAccent = isDark ? 'text-emerald-400' : 'text-emerald-600';

  return (
    <div className="flex items-center gap-3">
      <div
        className={`flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br ${ring} shadow-md`}
        aria-hidden
      >
        <svg className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2.5">
          <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <div>
        <p className={`text-[10px] font-semibold uppercase tracking-[0.2em] ${isDark ? 'text-sky-300/80' : 'text-sky-600/90'}`}>
          preparacao enem
        </p>
        <p className={`text-xl font-bold tracking-tight sm:text-2xl ${titleMain}`}>
          ENEM<span className={titleAccent}>.</span>pratica
        </p>
      </div>
    </div>
  );
}
