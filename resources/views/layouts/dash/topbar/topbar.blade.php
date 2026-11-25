@props([
	'title' => null,
	'description' => null,
])

<x-layout.header>
	<x-slot:left>
		@if ($title)
			<div class="hidden min-w-0 md:block">
				<x-dash::title :title="$title" :description="$description" compact />
			</div>
		@endif
	</x-slot:left>
	<x-slot:right>
		<div class="flex items-center gap-2 sm:gap-3">
			<div
				x-data="tallstackui_darkTheme()"
				x-effect="window.tsuiApplyTheme?.(darkTheme)"
			>
				<x-theme-switch simple only-icons sm />
			</div>

			<x-dash::topbar.locale-switch />

			<x-dash::notifications />

			<div class="hidden h-6 w-px bg-slate-200 dark:bg-slate-700 sm:block" aria-hidden="true"></div>

			@auth
				<x-dropdown>
					<x-slot:action>
						<button
							type="button"
							class="flex cursor-pointer items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 dark:hover:bg-slate-800"
							x-on:click="show = !show"
						>
							<x-avatar
								:model="auth()->user()"
								background="52678c"
								:options="['color' => 'fff']"
								sm
								borderless
								presence
							/>
							<div class="hidden text-left sm:block">
								<p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</p>
								<p class="max-w-40 truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
							</div>
							<x-icon name="chevron-down" class="hidden h-4 w-4 text-slate-400 sm:block" />
						</button>
					</x-slot:action>
					<x-dropdown.items :text="__('Change password')" :href="route('password.change')" />
					<x-dropdown.items :text="__('Logout')" :href="route('logout')" separator />
				</x-dropdown>
			@endauth
		</div>
	</x-slot:right>
</x-layout.header>
