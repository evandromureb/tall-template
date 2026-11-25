<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Livewire;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDashLayoutComponents();
        $this->configureTallStackUi();
        $this->configurePasswordRules();
        $this->configureLivewirePersistentMiddleware();
        $this->configureRateLimiting();
    }

    /**
     * Limite de requisições por minuto do grupo web, por usuário ou por IP.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('web', fn (Request $request): Limit => Limit::perMinute(120)
            ->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip())));
    }

    /**
     * As ações do Livewire também exigem e-mail confirmado e sessão válida após troca de senha,
     * não só o carregamento da página.
     */
    private function configureLivewirePersistentMiddleware(): void
    {
        Livewire::addPersistentMiddleware([AuthenticateSession::class, EnsureEmailIsVerified::class]);
    }

    /**
     * Regras padrão de senha (Password::defaults()) usadas no cadastro, na redefinição e na alteração de senha.
     * As dicas do campo de senha (`:rules` do x-password) espelham estas regras.
     */
    private function configurePasswordRules(): void
    {
        Password::defaults(fn (): Password => Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised());
    }

    /**
     * Registra os componentes anônimos dos layouts: resources/views/layouts/dash ("dash") e resources/views/layouts/auth ("auth").
     */
    private function configureDashLayoutComponents(): void
    {
        Blade::anonymousComponentPath(resource_path('views/layouts/dash'), 'dash');
        Blade::anonymousComponentPath(resource_path('views/layouts/auth'), 'auth');
    }

    /**
     * Aplica a soft customization dos componentes do TallStackUI usados no layout.
     */
    private function configureTallStackUi(): void
    {
        TallStackUi::customize('sideBar')
            ->block([
                'desktop.wrapper.first.size' => 'md:w-60',
                'desktop.sizes.expanded' => 'w-60',
                'desktop.wrapper.second' => 'relative flex grow flex-col overflow-hidden border-r border-slate-800/80 bg-slate-950 pb-4 transition-[width] duration-300 before:pointer-events-none before:absolute before:inset-x-0 before:top-0 before:h-40 before:bg-gradient-to-b before:from-primary-400/15 before:to-transparent sidebar-light:border-slate-200 sidebar-light:bg-white sidebar-light:before:from-primary-500/10',
                'desktop.footer' => 'shrink-0 overflow-hidden px-3',
                'desktop.wrapper.fifth' => 'flex flex-1 flex-col gap-y-0.5 px-2.5',
                'desktop.wrapper.items' => 'flex min-h-0 flex-1 flex-col gap-y-4 overflow-x-hidden overflow-y-auto',
                'mobile.wrapper.third' => 'relative mr-16 flex w-full max-w-60 flex-1',
                'mobile.wrapper.fourth' => 'relative flex grow flex-col overflow-hidden bg-slate-950 pb-4 before:pointer-events-none before:absolute before:inset-x-0 before:top-0 before:h-40 before:bg-gradient-to-b before:from-primary-400/15 before:to-transparent sidebar-light:bg-white sidebar-light:before:from-primary-500/10',
                'mobile.footer' => 'shrink-0 px-3 pb-4',
                'mobile.wrapper.sixth' => 'flex flex-1 flex-col gap-y-0.5 px-2.5',
                'mobile.wrapper.items' => 'flex min-h-0 flex-1 flex-col gap-y-4 overflow-x-hidden overflow-y-auto',
                'mobile.backdrop' => 'fixed inset-0 bg-slate-950/70 backdrop-blur-sm sidebar-light:bg-slate-900/40',
            ]);

        TallStackUi::customize('layout')
            ->block([
                'wrapper.second.expanded' => 'md:pl-60',
                'main' => 'w-full p-6 sm:p-8 lg:p-10',
            ]);

        TallStackUi::customize('sideBar.item')
            ->block([
                'item.state.base' => 'group flex items-center rounded-lg px-2.5 py-1.5 text-[13px] font-medium transition-all duration-200',
                'item.state.gap' => 'gap-x-2',
                'item.state.current' => 'bg-primary-400/15 text-primary-200 shadow-[inset_3px_0_0_0] shadow-primary-300 ring-1 ring-primary-300/25 sidebar-light:bg-primary-500/10 sidebar-light:text-primary-700 sidebar-light:shadow-primary-500 sidebar-light:ring-primary-500/20',
                'item.state.normal' => 'text-slate-400 hover:bg-white/5 hover:text-slate-100 sidebar-light:text-slate-600 sidebar-light:hover:bg-slate-100 sidebar-light:hover:text-slate-900',
                'item.icon' => 'h-4 w-4 shrink-0 transition-colors duration-200 group-hover:text-primary-200 sidebar-light:group-hover:text-primary-600',
                'item.wrapper.border' => 'border-l border-slate-800 pl-2 sidebar-light:border-slate-200',
                'group.button' => 'flex w-full cursor-pointer items-center rounded-lg px-2.5 py-1.5 text-left text-[13px] font-medium text-slate-400 transition-all duration-200 hover:bg-white/5 hover:text-slate-100 sidebar-light:text-slate-600 sidebar-light:hover:bg-slate-100 sidebar-light:hover:text-slate-900',
                'group.button.gap' => 'gap-x-2',
                'group.group' => 'mt-0.5 space-y-0.5 px-2 pl-4',
                'group.icon.base' => 'h-4 w-4 shrink-0 text-slate-400 sidebar-light:text-slate-500',
                'group.icon.collapse.base' => 'ml-auto h-3.5 w-3.5 shrink-0 text-slate-500 transition-all sidebar-light:text-slate-400',
                'group.icon.collapse.rotate' => 'rotate-180 text-primary-200 sidebar-light:text-primary-600',
                'group.flyout.header' => 'sticky top-0 -mx-2 bg-slate-950 px-2 pt-2 pb-1 text-xs font-semibold tracking-wide text-slate-500 uppercase sidebar-light:bg-white sidebar-light:text-slate-400',
            ]);

        TallStackUi::customize('sideBar.separator')
            ->block([
                'simple.base' => 'overflow-hidden text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase whitespace-nowrap transition-all duration-150 sidebar-light:text-slate-400',
                'simple.wrapper' => 'flex overflow-hidden px-3 transition-all duration-150',
                'simple.wrapper.visible' => 'mb-2 max-h-12 py-2 opacity-100',
                'simple.wrapper.hidden' => 'mb-0 max-h-0 py-0 opacity-0',
                'line.wrapper.first.visible' => 'mb-2 max-h-12 opacity-100',
                'line.wrapper.first.hidden' => 'mb-0 max-h-0 opacity-0',
                'line-right.wrapper.first.visible' => 'mb-2 max-h-12 opacity-100',
                'line-right.wrapper.first.hidden' => 'mb-0 max-h-0 opacity-0',
                'line.border' => 'w-full border-t border-slate-800 sidebar-light:border-slate-200',
                'line.base' => 'overflow-hidden bg-slate-950 px-3 text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase whitespace-nowrap transition-all duration-150 sidebar-light:bg-white sidebar-light:text-slate-400',
                'line-right.border' => 'w-full border-t border-slate-800 sidebar-light:border-slate-200',
                'line-right.base' => 'overflow-hidden bg-slate-950 px-3 text-[11px] font-semibold tracking-[0.14em] text-slate-500 uppercase whitespace-nowrap transition-all duration-150 sidebar-light:bg-white sidebar-light:text-slate-400',
            ]);

        TallStackUi::customize('layout.header')
            ->block([
                'wrapper.base' => 'sticky top-0 z-40 flex shrink-0 items-center gap-x-4 border-b border-slate-200/80 bg-white/80 px-4 backdrop-blur-xl sm:gap-x-6 sm:px-6 lg:px-8 tsui-scrollbar-bleed dark:border-slate-800 dark:bg-slate-950/80',
            ]);

        TallStackUi::customize('tab')
            ->block('base.wrapper')
            ->replace([
                'shadow-md' => 'shadow-sm ring-1 ring-slate-200/70 dark:ring-slate-700/60',
                'dark:bg-dark-800' => 'dark:bg-[#283548]',
            ]);

        TallStackUi::customize('tab')
            ->block('item.unselect')
            ->replace('dark:text-dark-500', 'dark:text-dark-400');

        TallStackUi::customize('card')
            ->block('wrapper.second')
            ->replace([
                'shadow-md' => 'shadow-sm ring-1 ring-slate-200/70 dark:ring-slate-700/60',
                'dark:bg-dark-800' => 'dark:bg-[#283548]',
            ])
            ->append('rounded-2xl');
    }
}
