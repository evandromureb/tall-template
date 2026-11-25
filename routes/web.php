<?php

use App\Http\Controllers\UpdateLocaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->name('register');
    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token?}', 'pages::auth.reset-password')->name('password.reset');
});

Route::middleware(['auth', 'auth.session'])->group(function (): void {
    Route::livewire('/verify-email', 'pages::auth.verify-email')->name('verification.notice');
    Route::livewire('/verify-email/{id}/{hash}', 'pages::auth.verify-email')
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::livewire('/logout', 'pages::auth.logout')->name('logout');
});

Route::middleware(['auth', 'auth.session', 'verified'])->group(function (): void {
    Route::livewire('/', 'dashboard')->name('dashboard');
    Route::livewire('/change-password', 'pages::auth.change-password')->name('password.change');
});

Route::post('/locale', UpdateLocaleController::class)->name('locale.update');

Route::fallback(fn () => abort(404));
