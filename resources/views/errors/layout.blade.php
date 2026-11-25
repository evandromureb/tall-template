<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex">

		<title>@yield('title') · {{ config('app.name') }}</title>

		<script>
			(() => {
				const stored = localStorage.getItem('dark-theme');
				const dark = stored === 'true' || stored === 'dark'
					|| (stored === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

				document.documentElement.classList.toggle('dark', dark);
			})();
		</script>

		@vite(['resources/css/app.css'])
	</head>
	<body class="bg-slate-100 font-sans text-slate-700 antialiased dark:bg-slate-950 dark:text-slate-300">
		<main class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
			<div class="flex w-full max-w-md flex-col items-center gap-6 rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200/70 dark:bg-slate-900 dark:ring-slate-800">
				<span class="text-5xl font-extrabold tracking-tight text-primary-500">@yield('code')</span>

				<div class="flex flex-col gap-2">
					<h1 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl dark:text-white">@yield('title')</h1>
					<p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">@yield('message')</p>
				</div>

				<div class="flex w-full flex-col gap-3 sm:flex-row sm:justify-center">
					@hasSection('retry')
						<a href="" onclick="window.location.reload(); return false;" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500">
							{{ __('Try again') }}
						</a>
					@endif
					<a href="{{ url('/') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 dark:text-slate-200 dark:ring-slate-700 dark:hover:bg-slate-800">
						{{ __('Go to home page') }}
					</a>
				</div>
			</div>

			<p class="mt-6 text-xs text-slate-500 dark:text-slate-400">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
		</main>
	</body>
</html>
