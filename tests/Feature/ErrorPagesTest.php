<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Route::middleware('web')->get('/_test/error/{code}', fn (int $code) => abort($code, 'SQLSTATE detalhe interno'));
});

test('renders the standard error page for each HTTP code', function (int $code, string $title, bool $offersRetry): void {
    $response = $this->get("/_test/error/{$code}")
        ->assertStatus($code)
        ->assertSee($title)
        ->assertSee('Ir para a página inicial')
        ->assertDontSee('SQLSTATE detalhe interno');

    $offersRetry ? $response->assertSee('Tentar novamente') : $response->assertDontSee('Tentar novamente');
})->with([
    '403' => [403, 'Acesso negado', false],
    '404' => [404, 'Página não encontrada', false],
    '419' => [419, 'Página expirada', true],
    '429' => [429, 'Muitas requisições', true],
    '500' => [500, 'Erro no servidor', true],
    '503' => [503, 'Serviço indisponível', true],
]);

test('shows the error page in the language selected by the user', function (): void {
    $this->withSession([SetLocale::SESSION_KEY => 'es'])
        ->get('/rota-que-nao-existe')
        ->assertNotFound()
        ->assertSee('<html lang="es">', false)
        ->assertSee('La página que buscas no existe');
});

test('sets the language before validating the CSRF token, so the 419 page is translated', function (): void {
    $middleware = resolve('router')->gatherRouteMiddleware(Route::getRoutes()->getByName('locale.update'));

    expect(array_search(SetLocale::class, $middleware))
        ->toBeLessThan(array_search(PreventRequestForgery::class, $middleware));
});
