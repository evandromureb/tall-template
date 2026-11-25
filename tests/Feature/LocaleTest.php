<?php

use App\Http\Middleware\SetLocale;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Symfony\Component\Finder\SplFileInfo;

uses(RefreshDatabase::class);

test('uses pt-BR by default', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<html lang="pt-BR">', false)
        ->assertSee('Acesse sua conta');
});

test('lets a guest switch the language for the session', function (string $locale, string $heading): void {
    $this->from(route('login'))
        ->post(route('locale.update'), ['locale' => $locale])
        ->assertRedirect(route('login'))
        ->assertSessionHas(SetLocale::SESSION_KEY, $locale);

    $this->get(route('login'))->assertSee($heading);
})->with([
    'english' => ['en', 'Access your account'],
    'spanish' => ['es', 'Accede a tu cuenta'],
]);

test('rejects a language that is not available', function (): void {
    $this->post(route('locale.update'), ['locale' => 'fr'])
        ->assertSessionHasErrors('locale')
        ->assertSessionMissing(SetLocale::SESSION_KEY);
});

test('persists the authenticated user language across sessions', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('locale.update'), ['locale' => 'es']);

    expect($user->fresh()->locale)->toBe('es');

    $this->flushSession();

    $this->actingAs($user->fresh())
        ->get(route('password.change'))
        ->assertSee('Cambiar contraseña');
});

test('shows validation messages in the selected language', function (): void {
    App::setLocale('es');

    Livewire::test('pages::auth.login')
        ->call('login')
        ->assertHasErrors(['email' => 'El campo correo electrónico es obligatorio.']);
});

test('stores the current language on registration and sends notifications in it', function (): void {
    Notification::fake();
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
    App::setLocale('en');

    Livewire::test('pages::auth.register')
        ->set(['name' => 'Maria Silva', 'email' => 'maria@empresa.com', 'terms' => true])
        ->set(['password' => 'Ativo#Seguro2026', 'password_confirmation' => 'Ativo#Seguro2026'])
        ->call('register');

    $user = User::query()->where('email', 'maria@empresa.com')->sole();

    expect($user->locale)->toBe('en');
    Notification::assertSentTo($user, VerifyEmail::class, fn (VerifyEmail $notification, array $channels, User $notifiable, ?string $locale): bool => $locale === 'en');
});

test('every translation key used by the application exists in each language file', function (): void {
    $files = collect(['resources/views', 'app'])
        ->flatMap(fn (string $directory): array => File::allFiles(base_path($directory)))
        ->push(new SplFileInfo(config_path('dashboard.php'), '', 'dashboard.php'));

    $usedKeys = $files
        ->flatMap(function (SplFileInfo $file): array {
            preg_match_all('/(?:__|trans_choice)\(\s*([\'"])((?:(?!\1).)+)\1/', $file->getContents(), $calls);
            preg_match_all("/#\[Title\('([^']+)'\)\]|'(?:heading|description|title|label)' => '([^']+)'/", $file->getContents(), $layoutTexts);

            return [...$calls[2], ...array_filter($layoutTexts[1]), ...array_filter($layoutTexts[2])];
        })
        ->reject(fn (string $key): bool => preg_match('/^[a-z_]+\.[a-z_.]+$/', $key) === 1)
        ->unique();

    foreach (array_keys(config('app.available_locales')) as $locale) {
        if ($locale === 'en') {
            continue;
        }

        $translations = json_decode(File::get(lang_path("{$locale}.json")), true, flags: JSON_THROW_ON_ERROR);

        expect($usedKeys->diff(array_keys($translations))->values()->all())->toBe([], "Chaves sem tradução em {$locale}.json");
    }
});
