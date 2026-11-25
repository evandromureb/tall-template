<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Altera o idioma da aplicação, guardando a escolha na sessão e no perfil do usuário autenticado.
 */
class UpdateLocaleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys(config('app.available_locales')))],
        ]);

        $request->session()->put(SetLocale::SESSION_KEY, $validated['locale']);

        $user = $request->user();

        if ($user instanceof User) {
            $user->update(['locale' => $validated['locale']]);
        }

        return back();
    }
}
