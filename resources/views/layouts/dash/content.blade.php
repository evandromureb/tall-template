@props([])

<div {{ $attributes->class(['w-full']) }}>
	<div class="w-full layout-boxed:mx-auto layout-boxed:max-w-7xl">
		{{ $slot }}
	</div>
</div>
