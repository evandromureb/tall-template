@php
	$title = isset($title) ? __($title) : null;
	$description = isset($description) ? __($description) : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<x-dash::base.head :title="$title ?? null" />
	<body
		class="bg-slate-100 font-sans antialiased dark:bg-slate-950"
		data-layout="fluid"
		data-sidebar="collapsible"
		data-sidebar-theme="dark"
		data-theme="light"
	>
		<script>
			document.body.dataset.theme = document.documentElement.dataset.theme || 'light';
		</script>
		<x-layout>
			<x-slot:top>
				<x-dialog />
				<x-toast />
			</x-slot:top>

			<x-slot:header>
				<x-dash::topbar :title="$title ?? null" :description="$description ?? null" />
			</x-slot:header>

			<x-slot:menu>
				<x-dash::sidebar collapsible />
			</x-slot:menu>

			<x-dash::content>
				@isset($title)
					<div class="mb-4 md:hidden">
						<x-dash::title
							:title="$title"
							:description="$description ?? null"
						/>
					</div>
				@endisset

				{{ $slot }}
			</x-dash::content>

			<x-slot:footer>
				<x-dash::base.footer />
			</x-slot:footer>
		</x-layout>
		<x-dash::base.scripts />
	</body>
</html>
