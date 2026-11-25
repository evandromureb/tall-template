<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function (): void {
    $response = $this->actingAs(User::factory()->create())->get('/');

    $response->assertOk();
});
