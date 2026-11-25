<?php

use App\Livewire\Component;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;

new #[Layout('layouts::auth')]
class extends Component
{
    public function mount(?string $id = null, ?string $hash = null): void
    {
        $user = Auth::user();

        if ($id !== null && $hash !== null) {
            abort_unless(
                (string) $user->getKey() === $id && hash_equals(sha1($user->getEmailForVerification()), $hash),
                403,
            );

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        }

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
        }
    }

    public function resend(): void
    {
        $key = 'verify-email:'.Auth::id();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            return;
        }

        RateLimiter::hit($key, 60);
        Auth::user()->sendEmailVerificationNotification();

        $this->toast()->success(__('Email resent'), __('We sent a new confirmation link.'))->send();
    }
};
?>

<div class="flex flex-col gap-4">
	<x-alert color="amber" icon="clock" light>
		<x-slot:title>{{ __('Confirmation pending') }}</x-slot:title>
		{!! __('Click the link sent to <strong>:email</strong> to activate your account.', ['email' => e(auth()->user()->email)]) !!}
		{{ __('If you cannot find the message, check your spam folder.') }}
	</x-alert>

	<x-auth::resend-button action="resend" :label="__('Resend confirmation email')" />

	<p class="text-center text-sm">
		<x-link :href="route('logout')" icon="arrow-left" :text="__('Sign out and use another account')" navigate sm />
	</p>
</div>
