<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')]
class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $this->addError('email', __('auth.throttle', ['seconds' => RateLimiter::availableIn($this->throttleKey())]));

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey(), 60);
            $this->addError('email', __('auth.failed'));
            $this->reset('password');

            return;
        }

        RateLimiter::clear($this->throttleKey());
        session()->regenerate();

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return ['email' => __('email'), 'password' => __('password')];
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

<form class="flex flex-col gap-4" wire:submit="login" novalidate>
	@if (session('status'))
		<x-alert color="green" icon="check-circle" light>{{ session('status') }}</x-alert>
	@endif

	@if (session('error'))
		<x-alert color="red" icon="no-symbol" light>{{ session('error') }}</x-alert>
	@endif

	<x-input type="email" :label="__('Email').' *'" :placeholder="__('you@company.com')" icon="envelope" autocomplete="username" required autofocus wire:model="email" />

	<x-password :label="__('Password').' *'" :placeholder="__('Enter your password')" autocomplete="current-password" required wire:model="password" />

	<div class="flex flex-wrap items-center justify-between gap-3">
		<x-checkbox :label="__('Remember me')" wire:model="remember" />
		<x-link :href="route('password.request')" :text="__('Forgot your password?')" navigate sm />
	</div>

	<x-button type="submit" color="primary" class="w-full" loading="login">{{ __('Sign in') }}</x-button>

	<p class="text-center text-sm text-slate-500 dark:text-slate-400">
		{{ __("Don't have an account?") }}
		<x-link :href="route('register')" :text="__('Sign up')" navigate sm />
	</p>
</form>
