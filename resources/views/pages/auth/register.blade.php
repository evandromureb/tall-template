<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')]
class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    public function register(): void
    {
        $validated = $this->validate();

        $throttleKey = 'register:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('email', __('Too many attempts. Try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($throttleKey)]));

            return;
        }

        RateLimiter::hit($throttleKey, 3600);

        try {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'locale' => app()->getLocale(),
            ]);
        } catch (UniqueConstraintViolationException) {
            $this->addError('email', __('validation.unique', ['attribute' => __('email')]));

            return;
        }

        event(new Registered($user));

        Auth::login($user);
        session()->regenerate();

        $this->redirectRoute('verification.notice', navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => __('name'),
            'email' => __('email'),
            'password' => __('password'),
            'terms' => __('terms of use'),
        ];
    }
};
?>

<form class="flex flex-col gap-4" wire:submit="register" novalidate>
	<x-input :label="__('Full name').' *'" :placeholder="__('Your name')" icon="user" autocomplete="name" required autofocus wire:model="name" />

	<x-input type="email" :label="__('Email').' *'" :placeholder="__('you@company.com')" icon="envelope" autocomplete="username" required wire:model="email" />

	<x-password :label="__('Password').' *'" :placeholder="__('Create a password')" autocomplete="new-password" :rules="['min:10', 'mixed', 'numbers', 'symbols']" generator required wire:model="password" />

	<x-password :label="__('Confirm password').' *'" :placeholder="__('Repeat the password')" autocomplete="new-password" required wire:model="password_confirmation" />

	<x-checkbox wire:model="terms">
		<x-slot:label>
			{!! __('I accept the :terms and the privacy policy', ['terms' => '<a href="#" class="font-medium text-primary-600 underline-offset-2 hover:underline dark:text-primary-300">'.e(__('terms of use')).'</a>']) !!} *
		</x-slot:label>
	</x-checkbox>

	<x-button type="submit" color="primary" class="w-full" loading="register">{{ __('Create account') }}</x-button>

	<p class="text-center text-sm text-slate-500 dark:text-slate-400">
		{{ __('Already have an account?') }}
		<x-link :href="route('login')" :text="__('Sign in')" navigate sm />
	</p>
</form>
