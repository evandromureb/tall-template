<?php

use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::get('/_test/client-ip', fn (): string => (string) request()->ip());
});

test('uses the forwarded client IP when the proxy is trusted', function (): void {
    config()->set('trustedproxy.proxies', '*');

    $this->withHeader('X-Forwarded-For', '203.0.113.9')->get('/_test/client-ip')->assertSee('203.0.113.9');
});

test('ignores the forwarded IP when no proxy is trusted', function (): void {
    config()->set('trustedproxy.proxies');

    $this->withHeader('X-Forwarded-For', '203.0.113.9')->get('/_test/client-ip')->assertDontSee('203.0.113.9');
});
