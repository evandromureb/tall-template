@props([
	'action',
	'label',
	'seconds' => 60,
	'startCounting' => false,
])

<div
	x-data="{
		remaining: 0,
		timer: null,
		start() {
			this.remaining = {{ (int) $seconds }};
			clearInterval(this.timer);
			this.timer = setInterval(() => {
				this.remaining = Math.max(0, this.remaining - 1);
				if (this.remaining === 0) clearInterval(this.timer);
			}, 1000);
		},
		async resend() {
			if (this.remaining > 0) return;
			await $wire.call(@js($action));
			this.start();
		},
	}"
	x-init="@js((bool) $startCounting) && start()"
>
	<x-button
		type="button"
		color="primary"
		outline
		class="w-full"
		x-on:click="resend"
		x-bind:disabled="remaining > 0"
		{{ $attributes }}
	>
		<span x-show="remaining === 0">{{ $label }}</span>
		<span x-show="remaining > 0" x-cloak aria-live="polite">{!! __('Resend in :seconds s', ['seconds' => '<span x-text="remaining"></span>']) !!}</span>
	</x-button>
</div>
