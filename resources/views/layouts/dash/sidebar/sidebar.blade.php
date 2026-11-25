@props([
	'collapsible' => false,
])

@php
	$isVisible = fn (array $entry): bool => ! isset($entry['can']) || \Illuminate\Support\Facades\Gate::allows(...(array) $entry['can']);
@endphp

<x-side-bar smart thin-scroll :collapsible="$collapsible">
	<x-slot:brand>
		<x-dash::sidebar.logo />
	</x-slot:brand>

	@if ($collapsible)
		<x-slot:brand-collapsed>
			<x-dash::sidebar.logo collapsed />
		</x-slot:brand-collapsed>
	@endif

	@foreach (config('dashboard.sidebar') as $entry)
		@continue(! $isVisible($entry))

		@switch($entry['type'])
			@case('separator')
				<x-dash::sidebar.separator :text="__($entry['label'])" :line="$entry['line'] ?? false" />
				@break

			@case('item')
				<x-dash::sidebar.item
					:text="__($entry['title'])"
					:icon="$entry['icon'] ?? null"
					:route="route($entry['route'])"
					:title="isset($entry['description']) ? __($entry['description']) : null"
				/>
				@break

			@case('drop')
				@php($children = array_filter($entry['items'], $isVisible))

				@if ($children)
					<x-dash::sidebar.drop :text="__($entry['title'])" :icon="$entry['icon'] ?? null">
						@foreach ($children as $child)
							<x-dash::sidebar.drop-item
								:text="__($child['title'])"
								:icon="$child['icon'] ?? null"
								:route="route($child['route'])"
								:title="isset($child['description']) ? __($child['description']) : null"
							/>
						@endforeach
					</x-dash::sidebar.drop>
				@endif
				@break
		@endswitch
	@endforeach
</x-side-bar>
