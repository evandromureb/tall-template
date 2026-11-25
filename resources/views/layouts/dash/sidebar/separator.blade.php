@props([
	'text' => null,
	'line' => false,
	'simple' => false,
])

<x-side-bar.separator
	:text="$text"
	:line="$line"
	:simple="$simple"
	{{ $attributes }}
/>
