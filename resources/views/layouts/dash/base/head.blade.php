@props([
	'title' => null,
])

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ $title ?? config('app.name') }}</title>

	<script>
		(() => {
			const stored = localStorage.getItem('dark-theme');
			const mode = stored === 'true' ? 'dark'
				: stored === 'false' ? 'light'
					: ['light', 'dark', 'system'].includes(stored) ? stored
						: 'light';
			const dark = mode === 'dark'
				|| (mode !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);

			document.documentElement.classList.toggle('dark', dark);
			document.documentElement.dataset.theme = dark ? 'dark' : 'light';
		})();
	</script>

	@fonts
	<tallstackui:script />
	@livewireStyles
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
