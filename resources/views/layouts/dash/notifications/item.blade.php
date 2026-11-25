@props([
	'id',
	'title',
	'body' => null,
	'time' => null,
	'icon' => 'bell',
	'tone' => 'primary',
	'unread' => false,
	'href' => null,
])

@php
	$tones = [
		'primary' => [
			'box' => 'bg-primary-100 dark:bg-primary-500/20',
			'icon' => 'text-primary-600 dark:text-primary-300',
		],
		'sky' => [
			'box' => 'bg-sky-100 dark:bg-sky-500/20',
			'icon' => 'text-sky-600 dark:text-sky-300',
		],
		'amber' => [
			'box' => 'bg-amber-100 dark:bg-amber-500/20',
			'icon' => 'text-amber-600 dark:text-amber-300',
		],
		'emerald' => [
			'box' => 'bg-emerald-100 dark:bg-emerald-500/20',
			'icon' => 'text-emerald-600 dark:text-emerald-300',
		],
		'rose' => [
			'box' => 'bg-rose-100 dark:bg-rose-500/20',
			'icon' => 'text-rose-600 dark:text-rose-300',
		],
	];

	$toneClasses = $tones[$tone] ?? $tones['primary'];
	$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
	@if ($href)
		href="{{ $href }}"
	@endif
	{{ $attributes->class([
		'group flex w-full items-start gap-3 border-b border-slate-200/80 px-3.5 py-3 text-left last:border-b-0 transition dark:border-slate-700/80',
		$unread
			? 'bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800/70 dark:hover:bg-slate-700/80'
			: 'bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800/60',
	]) }}
>
	<div @class([
		'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl',
		$toneClasses['box'],
	])>
		<x-icon :name="$icon" @class(['h-4 w-4', $toneClasses['icon']]) />
	</div>

	<div class="min-w-0 flex-1">
		<div class="flex items-start justify-between gap-3">
			<p @class([
				'text-[13px] leading-snug text-slate-900 dark:text-slate-50',
				'font-semibold' => $unread,
				'font-medium' => ! $unread,
			])>
				{{ $title }}
			</p>

			@if ($unread)
				<span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-sky-500 dark:bg-sky-400" aria-hidden="true"></span>
			@endif
		</div>

		@if ($body)
			<p class="mt-1 line-clamp-2 text-[12px] leading-relaxed text-slate-600 dark:text-slate-300">
				{{ $body }}
			</p>
		@endif

		@if ($time)
			<p class="mt-1.5 text-[11px] font-medium text-slate-500 dark:text-slate-400">
				{{ $time }}
			</p>
		@endif
	</div>
</{{ $tag }}>
