<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Define o idioma da requisição: preferência do usuário autenticado, depois a
 * escolha guardada na sessão (visitantes) e, por fim, o idioma padrão da aplicação.
 */
class SetLocale
{
    public const string SESSION_KEY = 'locale';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $locale = collect([
            $user instanceof User ? $user->locale : null,
            $request->session()->get(self::SESSION_KEY),
        ])->first(fn (?string $locale): bool => self::isAvailable($locale));

        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Indica se o idioma informado está entre os disponíveis na aplicação.
     */
    public static function isAvailable(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, config('app.available_locales'));
    }
}
