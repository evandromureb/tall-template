<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Gate::define('seeReports', fn (User $user): bool => $user->email === 'admin@empresa.com');

    config()->set('dashboard.sidebar', [
        ['type' => 'separator', 'label' => 'Grupo Configurado'],
        ['type' => 'item', 'title' => 'Item Configurado', 'icon' => 'home', 'route' => 'dashboard', 'description' => 'Descrição do item'],
        ['type' => 'item', 'title' => 'Relatórios Restritos', 'icon' => 'chart-bar', 'route' => 'password.change', 'can' => ['seeReports']],
        [
            'type' => 'drop',
            'title' => 'Menu Configurado',
            'icon' => 'cog-6-tooth',
            'items' => [['type' => 'drop-item', 'title' => 'Subitem Configurado', 'icon' => 'user', 'route' => 'password.change']],
        ],
        [
            'type' => 'drop',
            'title' => 'Menu Sem Filhos Visíveis',
            'icon' => 'lock-closed',
            'items' => [['type' => 'drop-item', 'title' => 'Subitem Restrito', 'route' => 'password.change', 'can' => ['seeReports']]],
        ],
    ]);
});

test('renders separators, items and drops from the dashboard config', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Grupo Configurado')
        ->assertSee('Item Configurado')
        ->assertSee('title="Descrição do item"', false)
        ->assertSee('Menu Configurado')
        ->assertSee('Subitem Configurado');
});

test('hides items, drop items and empty drops the user cannot access', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Relatórios Restritos')
        ->assertDontSee('Subitem Restrito')
        ->assertDontSee('Menu Sem Filhos Visíveis');
});

test('shows restricted entries when the gate allows', function (): void {
    $this->actingAs(User::factory()->create(['email' => 'admin@empresa.com']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Relatórios Restritos')
        ->assertSee('Menu Sem Filhos Visíveis')
        ->assertSee('Subitem Restrito');
});

test('translates the configured labels to the current locale', function (): void {
    config()->set('dashboard.sidebar', [['type' => 'item', 'title' => 'Application', 'icon' => 'home', 'route' => 'dashboard']]);

    $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertSee('Aplicação');
});
