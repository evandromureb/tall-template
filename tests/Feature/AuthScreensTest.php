<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
});

const STRONG_PASSWORD = 'Ativo#Seguro2026';

test('renders the guest screens', function (string $route, string $heading): void {
    $this->get(route($route))
        ->assertOk()
        ->assertSee($heading);
})->with([
    'login' => ['login', 'Acesse sua conta'],
    'register' => ['register', 'Crie sua conta'],
    'forgot password' => ['password.request', 'Esqueceu sua senha?'],
    'reset password' => ['password.reset', 'Redefinir senha'],
]);

test('redirects guests from authenticated screens to login', function (string $route): void {
    $this->get(route($route))->assertRedirect(route('login'));
})->with(['verification.notice', 'password.change', 'logout']);

test('sends users with an unverified e-mail to the verification notice', function (string $route): void {
    $this->actingAs(User::factory()->unverified()->create())
        ->get(route($route))
        ->assertRedirect(route('verification.notice'));
})->with(['dashboard', 'password.change']);

test('keeps the verification notice and the logout available to users with an unverified e-mail', function (string $route): void {
    $this->actingAs(User::factory()->unverified()->create())
        ->get(route($route))
        ->assertOk();
})->with(['verification.notice', 'logout']);

test('renders the authenticated screens', function (string $route, string $heading): void {
    $this->actingAs(User::factory()->create())
        ->get(route($route))
        ->assertOk()
        ->assertSee($heading);
})->with([
    'change password' => ['password.change', 'Alterar senha'],
    'logout' => ['logout', 'Sair do sistema'],
]);

test('loads the auth page texts from the dashboard config by route', function (): void {
    config()->set('dashboard.auth.login', [
        'route' => 'login',
        'title' => 'Título Configurado',
        'heading' => 'Cabeçalho Configurado',
        'description' => 'Descrição Configurada',
    ]);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<title>Título Configurado</title>', false)
        ->assertSee('Cabeçalho Configurado')
        ->assertSee('Descrição Configurada');
});

test('redirects authenticated users away from the login screen', function (): void {
    $this->actingAs(User::factory()->create())
        ->get(route('login'))
        ->assertRedirect();
});

describe('login', function (): void {
    test('requires e-mail and password with pt-BR messages', function (): void {
        Livewire::test('pages::auth.login')
            ->call('login')
            ->assertHasErrors(['email' => 'O campo e-mail é obrigatório.', 'password' => 'O campo senha é obrigatório.']);
    });

    test('rejects invalid credentials and clears the password', function (): void {
        $user = User::factory()->create();

        Livewire::test('pages::auth.login')
            ->set(['email' => $user->email, 'password' => 'errada'])
            ->call('login')
            ->assertHasErrors(['email' => 'Estas credenciais não conferem com nossos registros.'])
            ->assertSet('password', '')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('blocks login after five failed attempts', function (): void {
        $user = User::factory()->create();
        $component = Livewire::test('pages::auth.login')->set('email', $user->email);

        foreach (range(1, 5) as $attempt) {
            $component->set('password', 'errada')->call('login');
        }

        $component->set('password', 'Pa$$w0rd!@123@!')
            ->call('login')
            ->assertHasErrors('email')
            ->assertSee('Muitas tentativas de login.');

        $this->assertGuest();
    });

    test('authenticates and redirects to the dashboard', function (): void {
        $user = User::factory()->create();

        Livewire::test('pages::auth.login')
            ->set(['email' => $user->email, 'password' => 'Pa$$w0rd!@123@!'])
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    });
});

describe('register', function (): void {
    test('rejects a password outside the default rules', function (): void {
        Livewire::test('pages::auth.register')
            ->set(['name' => 'Maria Silva', 'email' => 'maria@empresa.com', 'terms' => true])
            ->set(['password' => 'Curta#1a', 'password_confirmation' => 'Curta#1a'])
            ->call('register')
            ->assertHasErrors(['password' => 'O campo senha deve ter pelo menos 10 caracteres.']);

        $this->assertDatabaseCount('users', 0);
    });

    test('rejects an e-mail already registered', function (): void {
        User::factory()->create(['email' => 'maria@empresa.com']);

        Livewire::test('pages::auth.register')
            ->set(['name' => 'Maria Silva', 'email' => 'maria@empresa.com', 'terms' => true])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('register')
            ->assertHasErrors(['email' => 'Este e-mail já está em uso.']);
    });

    test('requires accepting the terms', function (): void {
        Livewire::test('pages::auth.register')
            ->set(['name' => 'Maria Silva', 'email' => 'maria@empresa.com', 'terms' => false])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('register')
            ->assertHasErrors(['terms' => 'É necessário aceitar os termos de uso.']);
    });

    test('blocks a sixth registration from the same IP', function (): void {
        Notification::fake();
        $register = fn (int $attempt) => Livewire::test('pages::auth.register')
            ->set(['name' => 'Maria Silva', 'email' => "maria{$attempt}@empresa.com", 'terms' => true])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('register');

        foreach (range(1, 5) as $attempt) {
            $register($attempt)->assertHasNoErrors();
        }

        $register(6)->assertHasErrors('email')->assertNoRedirect();

        expect(User::query()->where('email', 'maria6@empresa.com')->exists())->toBeFalse();
    });

    test('rejects an e-mail registered between the validation and the insert', function (): void {
        Notification::fake();
        User::creating(fn (User $user) => User::withoutEvents(fn () => User::factory()->create(['email' => $user->email])));

        Livewire::test('pages::auth.register')
            ->set(['name' => 'Maria Silva', 'email' => 'maria@empresa.com', 'terms' => true])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('register')
            ->assertHasErrors(['email' => 'Este e-mail já está em uso.'])
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('creates the user, logs in and asks for e-mail verification', function (): void {
        Notification::fake();

        Livewire::test('pages::auth.register')
            ->set(['name' => 'Maria Silva', 'email' => 'maria@empresa.com', 'terms' => true])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('verification.notice'));

        $user = User::query()->where('email', 'maria@empresa.com')->sole();
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);
    });
});

describe('forgot password', function (): void {
    test('rejects an invalid e-mail', function (): void {
        Livewire::test('pages::auth.forgot-password')
            ->set('email', 'invalido')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'Informe um e-mail válido.'])
            ->assertSet('linkSent', false);
    });

    test('sends the reset link to a registered user', function (): void {
        Notification::fake();
        $user = User::factory()->create();

        Livewire::test('pages::auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendResetLink')
            ->assertSet('linkSent', true)
            ->assertSee('Verifique seu e-mail');

        Notification::assertSentTo($user, ResetPassword::class);
    });

    test('blocks a sixth reset link request from the same IP', function (): void {
        Notification::fake();
        $component = Livewire::test('pages::auth.forgot-password')->set('email', 'alguem@empresa.com');

        foreach (range(1, 5) as $attempt) {
            $component->call('sendResetLink')->assertHasNoErrors();
        }

        $component->call('sendResetLink')->assertHasErrors('email');
    });

    test('shows the same confirmation for an unknown e-mail', function (): void {
        Notification::fake();

        Livewire::test('pages::auth.forgot-password')
            ->set('email', 'ninguem@empresa.com')
            ->call('sendResetLink')
            ->assertSet('linkSent', true);

        Notification::assertNothingSent();
    });
});

describe('reset password', function (): void {
    test('shows the invalid link state when the token is missing', function (): void {
        Livewire::test('pages::auth.reset-password')
            ->assertSee('Este link de redefinição é inválido ou está incompleto.')
            ->assertSee('Solicitar novo link');
    });

    test('rejects an invalid or expired token', function (): void {
        $user = User::factory()->create();

        Livewire::withQueryParams(['email' => $user->email])
            ->test('pages::auth.reset-password', ['token' => 'invalido'])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('resetPassword')
            ->assertHasErrors(['token' => 'Este link de redefinição é inválido ou expirou.'])
            ->assertNoRedirect();

        expect(Hash::check('Pa$$w0rd!@123@!', $user->fresh()->password))->toBeTrue();
    });

    test('resets the password and redirects to login', function (): void {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::withQueryParams(['email' => $user->email])
            ->test('pages::auth.reset-password', ['token' => $token])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        expect(Hash::check(STRONG_PASSWORD, $user->fresh()->password))->toBeTrue()
            ->and(session('status'))->toBe('Senha redefinida com sucesso. Faça login com a nova senha.');
    });

    test('ends the sessions opened before the reset', function (): void {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $previousSession = session()->all();

        Livewire::withQueryParams(['email' => $user->email])
            ->test('pages::auth.reset-password', ['token' => Password::createToken($user)])
            ->set(['password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('resetPassword');

        $this->actingAs($user->fresh())->withSession($previousSession)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    });
});

describe('change password', function (): void {
    test('rejects a wrong current password', function (): void {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::auth.change-password')
            ->set(['current_password' => 'errada', 'password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('changePassword')
            ->assertHasErrors(['current_password' => 'A senha atual está incorreta.'])
            ->assertSet('changed', false);

        expect(Hash::check('Pa$$w0rd!@123@!', $user->fresh()->password))->toBeTrue();
    });

    test('updates the password and shows the success state', function (): void {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::auth.change-password')
            ->set(['current_password' => 'Pa$$w0rd!@123@!', 'password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('changePassword')
            ->assertHasNoErrors()
            ->assertSee('Senha alterada');

        expect(Hash::check(STRONG_PASSWORD, $user->fresh()->password))->toBeTrue();
    });

    test('ends the other sessions, keeps the current one and rotates the remember token', function (): void {
        $user = User::factory()->create(['remember_token' => 'token-antigo']);
        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $otherDeviceSession = session()->all();

        Livewire::actingAs($user)
            ->test('pages::auth.change-password')
            ->set(['current_password' => 'Pa$$w0rd!@123@!', 'password' => STRONG_PASSWORD, 'password_confirmation' => STRONG_PASSWORD])
            ->call('changePassword')
            ->assertHasNoErrors();
        $currentSession = session()->all();

        $this->actingAs($user->fresh())->withSession($currentSession)->get(route('dashboard'))->assertOk();
        $this->actingAs($user->fresh())->withSession($otherDeviceSession)->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
        expect($user->fresh()->remember_token)->not->toBe('token-antigo');
    });
});

describe('verify email', function (): void {
    test('shows the pending state for an unverified user', function (): void {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Confirmação pendente')
            ->assertSee($user->email);
    });

    test('marks the e-mail as verified through the signed link', function (): void {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);

        $this->actingAs($user)->get($url)->assertRedirect(route('dashboard', absolute: false));

        expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    });

    test('forbids a link with the wrong hash', function (): void {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1('outro@empresa.com')]);

        $this->actingAs($user)->get($url)->assertForbidden();

        expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    });

    test('resends the verification e-mail', function (): void {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)->test('pages::auth.verify-email')->call('resend');

        Notification::assertSentTo($user, VerifyEmail::class);
    });
});

test('logs the user out and redirects to login', function (): void {
    Livewire::actingAs(User::factory()->create())
        ->test('pages::auth.logout')
        ->call('logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
