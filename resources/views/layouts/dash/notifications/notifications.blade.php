@props([
	'notifications' => null,
	'markAllUrl' => null,
	'centerUrl' => null,
])

@php
	$centerUrl ??= route('dashboard');

	/** @var array<int, array{id: string, title: string, body?: string, time?: string, icon?: string, tone?: string, unread?: bool, href?: string|null}> */
	$notifications ??= [];

	$unreadCount = collect($notifications)->where('unread', true)->count();
@endphp

<x-dropdown position="bottom-end" width="2xl">
	<x-slot:action>
		<button
			type="button"
			x-on:click="show = !show"
			class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
			aria-label="{{ __('System notifications') }}"
		>
			<span class="relative inline-block h-5 w-5">
				<x-icon name="bell" class="h-5 w-5" />

				@if ($unreadCount > 0)
					<span
						class="absolute h-2 w-2 rounded-full bg-primary-500 ring-2 ring-white dark:ring-slate-900"
						style="top: -2px; right: -2px; left: auto;"
					>
						<span class="sr-only">{{ trans_choice(':count unread', $unreadCount) }}</span>
					</span>
				@endif
			</span>
		</button>
	</x-slot:action>

	<div class="w-[22rem] overflow-hidden bg-white dark:bg-slate-900 sm:w-96">
		<div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-white px-3.5 py-3 dark:border-slate-700 dark:bg-slate-900">
			<div>
				<p class="text-sm font-semibold text-slate-900 dark:text-slate-50">{{ __('Notifications') }}</p>
				<p class="mt-0.5 text-[12px] text-slate-600 dark:text-slate-300">
					@if ($unreadCount > 0)
						{{ trans_choice(':count unread', $unreadCount) }}
					@else
						{{ __('Nothing pending') }}
					@endif
				</p>
			</div>

			@if ($unreadCount > 0 && $markAllUrl)
				<a
					href="{{ $markAllUrl }}"
					class="cursor-pointer rounded-md px-2 py-1 text-[11px] font-semibold text-sky-600 transition hover:bg-sky-100 dark:text-sky-300 dark:hover:bg-slate-800"
				>
					{{ __('Mark all as read') }}
				</a>
			@endif
		</div>

		<div class="max-h-80 overflow-y-auto bg-white soft-scrollbar dark:bg-slate-900">
			@forelse ($notifications as $notification)
				<x-dash::notifications.item
					:id="$notification['id']"
					:title="$notification['title']"
					:body="$notification['body'] ?? null"
					:time="$notification['time'] ?? null"
					:icon="$notification['icon'] ?? 'bell'"
					:tone="$notification['tone'] ?? 'primary'"
					:unread="$notification['unread'] ?? false"
					:href="$notification['href'] ?? null"
				/>
			@empty
				<x-dash::notifications.empty />
			@endforelse
		</div>

		<div class="border-t border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-900">
			<a
				href="{{ $centerUrl }}"
				class="flex items-center justify-center gap-1.5 rounded-lg px-2 py-2 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white"
			>
				{{ __('View notification center') }}
				<x-icon name="arrow-top-right-on-square" class="h-3.5 w-3.5" />
			</a>
		</div>
	</div>
</x-dropdown>
