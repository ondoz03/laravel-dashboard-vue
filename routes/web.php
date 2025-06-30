<?php

use App\Http\Controllers\MasterItem\MasterItemController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Master Items Routes
Route::middleware('auth')->group(function () {
    Route::get('master-items', [MasterItemController::class, 'index'])->name('master-items.index');
    Route::get('master-items/create', [MasterItemController::class, 'create'])->name('master-items.create');
    Route::post('master-items', [MasterItemController::class, 'store'])->name('master-items.store');
    Route::get('master-items/{masterItem}', [MasterItemController::class, 'show'])->name('master-items.show');
    Route::get('master-items/{masterItem}/edit', [MasterItemController::class, 'edit'])->name('master-items.edit');
    Route::patch('master-items/{masterItem}', [MasterItemController::class, 'update'])->name('master-items.update');
    Route::delete('master-items/{masterItem}', [MasterItemController::class, 'destroy'])->name('master-items.destroy');
});

// User Management Routes
Route::middleware('auth')->group(function () {
    Route::get('users', [App\Http\Controllers\User\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [App\Http\Controllers\User\UserController::class, 'create'])->name('users.create');
    Route::post('users', [App\Http\Controllers\User\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [App\Http\Controllers\User\UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [App\Http\Controllers\User\UserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}', [App\Http\Controllers\User\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [App\Http\Controllers\User\UserController::class, 'destroy'])->name('users.destroy');

    // User Preferences Routes
//    Route::post('user-preferences', [App\Http\Controllers\UserPreferenceController::class, 'store'])->name('user-preferences.store');
//    Route::get('user-preferences', [App\Http\Controllers\UserPreferenceController::class, 'show'])->name('user-preferences.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
