@props([
	'text',
	'icon' => null,
	'opened' => null,
])

<x-side-bar.item :text="$text" :icon="$icon" :opened="$opened" {{ $attributes }}>
	{{ $slot }}
</x-side-bar.item>
