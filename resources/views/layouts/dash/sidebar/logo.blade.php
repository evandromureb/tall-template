@props([
	'collapsed' => false,
])

@if ($collapsed)
	<div class="relative z-10 my-5 flex items-center justify-center">
		<div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-slate-400 to-slate-700 shadow-lg shadow-slate-950/50 ring-1 ring-white/10 sidebar-light:shadow-slate-300/40 sidebar-light:ring-slate-300/60">
			<span class="text-xs font-extrabold text-white">{{ Str::upper(Str::substr(config('app.name'), 0, 2)) }}</span>
		</div>
	</div>
@else
	<div class="relative z-10 mx-3 mt-5 mb-2 rounded-2xl border border-white/5 bg-white/5 p-3 backdrop-blur-sm sidebar-light:border-slate-200 sidebar-light:bg-slate-50">
		<div class="flex items-center gap-3">
			<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-400 to-slate-700 shadow-lg shadow-slate-950/50 ring-1 ring-white/10 sidebar-light:shadow-slate-300/40 sidebar-light:ring-slate-300/60">
				<span class="text-sm font-extrabold tracking-tight text-white">{{ Str::upper(Str::substr(config('app.name'), 0, 2)) }}</span>
			</div>
			<p class="min-w-0 truncate text-sm leading-tight font-bold tracking-tight text-white sidebar-light:text-slate-900">{{ config('app.name') }}</p>
		</div>
	</div>
@endif
