<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')]
class extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(?string $token = null): void
    {
        $this->token = (string) $token;
        $this->email = (string) request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user): void {
                $user->forceFill(['password' => $this->password, 'remember_token' => Str::random(60)])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('token', match ($status) {
                Password::RESET_THROTTLED => __('Please wait a moment before trying again.'),
                default => __('This reset link is invalid or has expired.'),
            });

            return;
        }

        session()->flash('status', __('Password reset successfully. Sign in with your new password.'));

        $this->redirectRoute('login', navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return ['email' => __('email'), 'password' => __('password')];
    }
};
?>

<div class="flex flex-col gap-4">
	@if ($token === '' || $email === '' || $errors->has('token'))
		<x-alert color="red" icon="link-slash" light>
			<x-slot:title>{{ __('Invalid link') }}</x-slot:title>
			{{ $errors->first('token') ?: __('This reset link is invalid or incomplete.') }} {{ __('Request a new recovery link.') }}
		</x-alert>

		<x-button :href="route('password.request')" color="primary" class="w-full" wire:navigate>{{ __('Request new link') }}</x-button>
	@else
		<form class="flex flex-col gap-4" wire:submit="resetPassword" novalidate>
			<x-input type="email" :label="__('Email')" icon="envelope" autocomplete="username" :value="$email" readonly />

			<x-password :label="__('New password').' *'" :placeholder="__('Create a new password')" autocomplete="new-password" :rules="['min:10', 'mixed', 'numbers', 'symbols']" generator required autofocus wire:model="password" />

			<x-password :label="__('Confirm password').' *'" :placeholder="__('Repeat the new password')" autocomplete="new-password" required wire:model="password_confirmation" />

			<x-button type="submit" color="primary" class="w-full" loading="resetPassword">{{ __('Reset password') }}</x-button>
		</form>
	@endif

	<x-auth::back-to-login />
</div>
