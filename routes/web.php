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
Route::middleware(['auth'])->group(function () {
    Route::get('master-items', [MasterItemController::class, 'index'])->name('master-items.index')->middleware('permission:view master items');
    Route::get('master-items/create', [MasterItemController::class, 'create'])->name('master-items.create')->middleware('permission:create master items');
    Route::post('master-items', [MasterItemController::class, 'store'])->name('master-items.store')->middleware('permission:create master items');
    Route::get('master-items/{masterItem}', [MasterItemController::class, 'show'])->name('master-items.show')->middleware('permission:view master items');
    Route::get('master-items/{masterItem}/edit', [MasterItemController::class, 'edit'])->name('master-items.edit')->middleware('permission:edit master items');
    Route::patch('master-items/{masterItem}', [MasterItemController::class, 'update'])->name('master-items.update')->middleware('permission:edit master items');
    Route::delete('master-items/{masterItem}', [MasterItemController::class, 'destroy'])->name('master-items.destroy')->middleware('permission:delete master items');
});

// User Management Routes
Route::middleware(['auth'])->group(function () {
    // Users
    Route::get('users', [App\Http\Controllers\User\UserController::class, 'index'])->name('users.index')->middleware('permission:view users');
    Route::get('users/create', [App\Http\Controllers\User\UserController::class, 'create'])->name('users.create')->middleware('permission:create users');
    Route::post('users', [App\Http\Controllers\User\UserController::class, 'store'])->name('users.store')->middleware('permission:create users');
    Route::get('users/{user}', [App\Http\Controllers\User\UserController::class, 'show'])->name('users.show')->middleware('permission:view users');
    Route::get('users/{user}/edit', [App\Http\Controllers\User\UserController::class, 'edit'])->name('users.edit')->middleware('permission:edit users');
    Route::patch('users/{user}', [App\Http\Controllers\User\UserController::class, 'update'])->name('users.update')->middleware('permission:edit users');
    Route::delete('users/{user}', [App\Http\Controllers\User\UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:delete users');

    // Roles
    Route::get('roles', [App\Http\Controllers\User\RoleController::class, 'index'])->name('roles.index')->middleware('permission:view roles');
    Route::get('roles/create', [App\Http\Controllers\User\RoleController::class, 'create'])->name('roles.create')->middleware('permission:create roles');
    Route::post('roles', [App\Http\Controllers\User\RoleController::class, 'store'])->name('roles.store')->middleware('permission:create roles');
    Route::get('roles/{role}', [App\Http\Controllers\User\RoleController::class, 'show'])->name('roles.show')->middleware('permission:view roles');
    Route::get('roles/{role}/edit', [App\Http\Controllers\User\RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:edit roles');
    Route::patch('roles/{role}', [App\Http\Controllers\User\RoleController::class, 'update'])->name('roles.update')->middleware('permission:edit roles');
    Route::delete('roles/{role}', [App\Http\Controllers\User\RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete roles');

    // Permissions
    Route::get('permissions', [App\Http\Controllers\User\PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:view permissions');
    Route::get('permissions/create', [App\Http\Controllers\User\PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:create permissions');
    Route::post('permissions', [App\Http\Controllers\User\PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:create permissions');
    Route::get('permissions/{permission}', [App\Http\Controllers\User\PermissionController::class, 'show'])->name('permissions.show')->middleware('permission:view permissions');
    Route::get('permissions/{permission}/edit', [App\Http\Controllers\User\PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:edit permissions');
    Route::patch('permissions/{permission}', [App\Http\Controllers\User\PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:edit permissions');
    Route::delete('permissions/{permission}', [App\Http\Controllers\User\PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:delete permissions');

    // User Preferences Routes
//    Route::post('user-preferences', [App\Http\Controllers\UserPreferenceController::class, 'store'])->name('user-preferences.store');
//  Route::get('user-preferences', [App\Http\Controllers\UserPreferenceController::class, 'show'])->name('user-preferences.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
