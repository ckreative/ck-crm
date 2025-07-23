<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-icons', function () {
    return view('test-icons');
});

Route::get('/test-alpine', function () {
    return view('test-alpine');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes (protected by auth and admin middleware)
Route::middleware(['auth', 'admin'])->group(function () {
    // App Settings
    Route::prefix('app-settings')->name('app-settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\AppSettingsController::class, 'index'])->name('index');
        
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\UserManagementController::class, 'index'])->name('index');
            Route::post('/invite', [App\Http\Controllers\UserManagementController::class, 'invite'])->name('invite');
            Route::post('/{invitation}/resend', [App\Http\Controllers\UserManagementController::class, 'resend'])->name('resend');
            Route::delete('/{invitation}', [App\Http\Controllers\UserManagementController::class, 'cancel'])->name('cancel');
        });
    });
});

// Public invitation routes
Route::prefix('invitations')->name('invitations.')->group(function () {
    Route::get('/{token}', [App\Http\Controllers\InvitationController::class, 'show'])->name('show');
    Route::post('/{token}', [App\Http\Controllers\InvitationController::class, 'accept'])->name('accept');
});

require __DIR__.'/auth.php';
