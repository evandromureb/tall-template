@props([
	'text' => null,
])

<p {{ $attributes->class(['text-center text-sm']) }}>
	<x-link :href="route('login')" icon="arrow-left" :text="$text ?? __('Back to login')" navigate sm />
</p>
