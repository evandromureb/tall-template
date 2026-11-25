<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')]
class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $changed = false;

    public function changePassword(): void
    {
        $validated = $this->validate();

        Auth::user()->forceFill(['password' => $validated['password'], 'remember_token' => Str::random(60)])->save();

        Auth::logoutOtherDevices($validated['password']);

        session()->put('password_hash_'.Auth::getDefaultDriver(), Auth::hashPasswordForCookie(Auth::user()->getAuthPassword()));

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->changed = true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return ['current_password' => __('current password'), 'password' => __('new password')];
    }
};
?>

<div class="flex flex-col gap-4">
	@if ($changed)
		<x-alert color="green" icon="check-circle" light>
			<x-slot:title>{{ __('Password changed') }}</x-slot:title>
			{{ __('Your password was changed successfully. Use the new password next time you sign in.') }}
		</x-alert>

		<x-button :href="route('dashboard')" color="primary" class="w-full" wire:navigate>{{ __('Go to dashboard') }}</x-button>
	@else
		<form class="flex flex-col gap-4" wire:submit="changePassword" novalidate>
			<input type="text" name="username" value="{{ auth()->user()->email }}" autocomplete="username" class="sr-only" tabindex="-1" aria-hidden="true" readonly>

			<x-password :label="__('Current password').' *'" :placeholder="__('Enter your current password')" autocomplete="current-password" required autofocus wire:model="current_password" />

			<x-password :label="__('New password').' *'" :placeholder="__('Create a new password')" autocomplete="new-password" :rules="['min:10', 'mixed', 'numbers', 'symbols']" generator required wire:model="password" />

			<x-password :label="__('Confirm new password').' *'" :placeholder="__('Repeat the new password')" autocomplete="new-password" required wire:model="password_confirmation" />

			<x-button type="submit" color="primary" class="w-full" loading="changePassword">{{ __('Save new password') }}</x-button>
		</form>
	@endif

	<p class="text-center text-sm">
		<x-link :href="route('dashboard')" icon="arrow-left" :text="__('Back to dashboard')" navigate sm />
	</p>
</div>
