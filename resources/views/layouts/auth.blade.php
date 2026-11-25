@php
	$page = collect(config('dashboard.auth'))->first(fn (array $page): bool => request()->routeIs($page['route'])) ?? [];
	$title = isset($title) ? __($title) : (isset($page['title']) ? __($page['title']) : null);
	$heading = isset($heading) ? __($heading) : (isset($page['heading']) ? __($page['heading']) : null);
	$description = isset($description) ? __($description) : (isset($page['description']) ? __($page['description']) : null);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<x-dash::base.head :title="$title ?? null" />
	<body class="bg-slate-100 font-sans text-slate-700 antialiased dark:bg-slate-950 dark:text-slate-300" data-theme="light">
		<script>
			document.body.dataset.theme = document.documentElement.dataset.theme || 'light';
		</script>
		<x-toast />

		<div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10">
			<div class="pointer-events-none absolute inset-0" aria-hidden="true">
				<div class="absolute -top-24 -left-24 size-72 rounded-full bg-primary-400/15 blur-3xl"></div>
				<div class="absolute -right-20 -bottom-28 size-80 rounded-full bg-slate-400/15 blur-3xl"></div>
			</div>

			<div
				class="absolute top-4 right-4 z-10 sm:top-6 sm:right-6"
				x-data="tallstackui_darkTheme()"
				x-effect="window.tsuiApplyTheme?.(darkTheme)"
			>
				<x-theme-switch simple only-icons sm />
			</div>

			<div class="absolute top-4 left-4 z-10 sm:top-6 sm:left-6">
				<x-dash::topbar.locale-switch />
			</div>

			<main class="relative z-10 flex w-full max-w-md flex-col items-center gap-6">
				<a href="{{ route('login') }}" class="flex items-center gap-3 rounded-xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary-500">
					<span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-slate-400 to-slate-700 shadow-lg shadow-slate-400/30 ring-1 ring-white/10 dark:shadow-slate-950/50">
						<span class="text-sm font-extrabold tracking-tight text-white">{{ Str::upper(Str::substr(config('app.name'), 0, 2)) }}</span>
					</span>
					<span class="text-base leading-tight font-bold tracking-tight text-slate-900 dark:text-white">{{ config('app.name') }}</span>
				</a>

				<x-card class="w-full">
					<div class="flex flex-col gap-6 p-2 sm:p-4">
						@if (filled($heading ?? null) || filled($description ?? null))
							<div class="flex flex-col gap-2 text-center">
								@if (filled($heading ?? null))
									<h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
										{{ $heading }}
									</h1>
								@endif
								@if (filled($description ?? null))
									<p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">
										{{ $description }}
									</p>
								@endif
							</div>
						@endif

						{{ $slot }}
					</div>
				</x-card>

				<p class="text-center text-xs text-slate-500 dark:text-slate-400">
					&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
				</p>
			</main>
		</div>

		<x-dash::base.scripts />
	</body>
</html>
