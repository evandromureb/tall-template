@props([
	'appName' => null,
])

@php
	$appName ??= config('app.name');
@endphp

<footer {{ $attributes->class(['border-t border-slate-200/80 bg-white/70 px-6 py-4 backdrop-blur-sm sm:px-8 lg:px-10 dark:border-slate-800 dark:bg-slate-950/70']) }}>
	<div class="flex w-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between layout-boxed:mx-auto layout-boxed:max-w-7xl">
		<div class="flex items-center gap-2.5">
			<div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-slate-400 to-slate-700 text-[10px] font-extrabold text-white ring-1 ring-slate-300/40 dark:ring-white/10">
				{{ Str::upper(Str::substr($appName, 0, 2)) }}
			</div>
			<p class="text-xs font-semibold text-slate-700 dark:text-slate-200">
				{{ $appName }}
			</p>
		</div>

		<nav class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[12px] font-medium text-slate-500 dark:text-slate-400">
			<a href="{{ route('dashboard') }}" class="transition hover:text-primary-600 dark:hover:text-primary-300">
				{{ __('Docs') }}
			</a>
			<a href="{{ route('dashboard') }}" class="transition hover:text-primary-600 dark:hover:text-primary-300">
				{{ __('Starter Kit') }}
			</a>
			<a href="{{ route('dashboard') }}" class="transition hover:text-primary-600 dark:hover:text-primary-300">
				{{ __('Welcome') }}
			</a>
		</nav>

		<p class="text-[11px] text-slate-400 dark:text-slate-500">
			&copy; {{ date('Y') }} {{ $appName }}. {{ __('All rights reserved.') }}
		</p>
	</div>
</footer>
