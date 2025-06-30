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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
