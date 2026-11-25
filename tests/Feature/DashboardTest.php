<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('renders the dashboard livewire page', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeLivewire('dashboard')
        ->assertSee('DASHBOARD');
});

test('shows the page title and description in the layout', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('<title>Dashboard</title>', false)
        ->assertSee('Visão geral.');
});

test('mounts the dashboard component', function (): void {
    Livewire::test('dashboard')
        ->assertOk()
        ->assertSee('DASHBOARD');
});

test('shows the authenticated user menu in the topbar', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->email);
});

test('redirects guests to the login page', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
