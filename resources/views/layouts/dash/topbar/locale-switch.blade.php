<x-dropdown position="bottom-end">
	<x-slot:action>
		<button
			type="button"
			x-on:click="show = !show"
			class="inline-flex h-9 cursor-pointer items-center justify-center gap-1 rounded-xl px-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white"
			aria-label="{{ __('Language') }}"
			title="{{ __('Language') }}"
		>
			<x-icon name="language" class="h-5 w-5" />
			<span class="text-xs font-semibold uppercase">{{ str(app()->getLocale())->before('_') }}</span>
		</button>
	</x-slot:action>

	@foreach (config('app.available_locales') as $code => $name)
		<form method="POST" action="{{ route('locale.update') }}">
			@csrf
			<input type="hidden" name="locale" value="{{ $code }}">
			<button
				type="submit"
				lang="{{ str_replace('_', '-', $code) }}"
				@class([
					'flex w-full cursor-pointer items-center justify-between gap-3 px-4 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
					'font-semibold' => app()->getLocale() === $code,
				])
				@if (app()->getLocale() === $code) aria-current="true" @endif
			>
				{{ $name }}
				@if (app()->getLocale() === $code)
					<x-icon name="check" class="h-4 w-4 text-primary-500" />
				@endif
			</button>
		</form>
	@endforeach
</x-dropdown>
