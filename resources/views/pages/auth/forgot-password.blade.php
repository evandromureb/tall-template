<?php

use App\Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;

new #[Layout('layouts::auth')]
class extends Component
{
    public string $email = '';

    public bool $linkSent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        $throttleKey = 'forgot-password:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('email', __('Too many attempts. Try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($throttleKey)]));

            return;
        }

        RateLimiter::hit($throttleKey);

        Password::sendResetLink(['email' => $this->email]);

        $this->linkSent = true;
    }

    public function resend(): void
    {
        $this->sendResetLink();

        if ($this->getErrorBag()->has('email')) {
            return;
        }

        $this->toast()->success(__('Link resent'), __('If the email is registered, we have sent a new link.'))->send();
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return ['email' => ['required', 'string', 'email', 'max:255']];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return ['email' => __('email')];
    }
};
?>

<div class="flex flex-col gap-4">
	@if (! $linkSent)
		<form class="flex flex-col gap-4" wire:submit="sendResetLink" novalidate>
			<x-input type="email" :label="__('Email').' *'" :placeholder="__('you@company.com')" icon="envelope" autocomplete="username" required autofocus wire:model="email" />

			<x-button type="submit" color="primary" class="w-full" loading="sendResetLink">{{ __('Send recovery link') }}</x-button>
		</form>
	@else
		<x-alert color="green" icon="envelope-open" light>
			<x-slot:title>{{ __('Check your email') }}</x-slot:title>
			{!! __('If <strong>:email</strong> is registered, you will receive a link to reset your password. The link expires in :minutes minutes.', ['email' => e($email), 'minutes' => config('auth.passwords.users.expire')]) !!}
		</x-alert>

		<x-auth::resend-button action="resend" :label="__('Resend link')" :seconds="config('auth.passwords.users.throttle')" start-counting />
	@endif

	<x-auth::back-to-login />
</div>
