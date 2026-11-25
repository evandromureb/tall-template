@props([
    'title',
    'description' => null,
    'compact' => false,
])

@if ($compact)
	<div {{ $attributes->class(['min-w-0']) }}>
		<p class="truncate text-lg font-semibold tracking-tight text-slate-800 dark:text-white">
			{{ $title }}
		</p>
		@if ($description)
			<p class="truncate text-xs text-slate-500 dark:text-slate-400">
				{{ $description }}
			</p>
		@endif
	</div>
@else
	<div {{ $attributes->class(['space-y-2']) }}>
		<h1 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-5xl">
			{{ $title }}
		</h1>
		@if ($description)
			<p class="max-w-2xl text-base leading-relaxed text-slate-600 dark:text-slate-400">
				{{ $description }}
			</p>
		@endif
	</div>
@endif
