<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $renderSidebar = fn (): string => Blade::render(<<<'BLADE'
        <x-side-bar smart>
            <x-dash::sidebar.drop text="Settings" icon="cog-6-tooth">
                <x-dash::sidebar.drop-item text="Profile" icon="user" :route="route('_test.drop.child')" />
            </x-dash::sidebar.drop>
        </x-side-bar>
        BLADE);

    Route::middleware('web')->get('/_test/drop/child', $renderSidebar)->name('_test.drop.child');
    Route::middleware('web')->get('/_test/drop/other', $renderSidebar)->name('_test.drop.other');

    Route::getRoutes()->refreshNameLookups();
});

test('opens the drop when one of its child routes is active', function (): void {
    $this->get('/_test/drop/child')
        ->assertOk()
        ->assertSee('show : true', false);
});

test('keeps the drop closed when none of its child routes is active', function (): void {
    $this->get('/_test/drop/other')
        ->assertOk()
        ->assertSee('show : false', false);
});
