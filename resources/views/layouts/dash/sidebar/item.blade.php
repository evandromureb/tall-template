@props([
	'text',
	'icon' => null,
	'route' => null,
	'href' => null,
	'current' => false,
	'badge' => null,
	'badgeColor' => 'primary',
])

<x-side-bar.item
	:text="$text"
	:icon="$icon"
	:route="$route"
	:href="$href"
	:current="$current"
	:badge="$badge"
	:badge-color="$badgeColor"
	{{ $attributes }}
/>
