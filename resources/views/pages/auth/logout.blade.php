<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')]
class extends Component
{
    public function logout(): void
    {
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();
        session()->flash('status', __('You have signed out securely. See you soon!'));

        $this->redirectRoute('login', navigate: true);
    }
};
?>

<div class="flex flex-col gap-4">
	<x-button type="button" color="red" icon="arrow-right-start-on-rectangle" class="w-full" loading="logout" wire:click="logout">{{ __('Sign out') }}</x-button>

	<x-button :href="route('dashboard')" color="secondary" outline class="w-full" wire:navigate>{{ __('Cancel') }}</x-button>
</div>
