<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReviewsController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('reviews', [ReviewsController::class, 'index'])->middleware(['auth', 'verified'])->name('reviews');

Route::get('settings', [SettingsController::class, 'index'])->middleware(['auth', 'verified'])->name('settings');

Route::patch('settings/update/yandex_url', [SettingsController::class, 'updateYandexUrl'])
    ->middleware(['auth', 'verified'])
    ->name('settings.update.yandex_url');


