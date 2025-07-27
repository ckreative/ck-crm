<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganizationCalcomController;
use App\Http\Controllers\OrganizationSwitchController;
use App\Http\Controllers\OrganizationManagementController;
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
})->middleware(['auth', 'verified', 'ensure.has.selected.organization'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Organization selection routes (not protected by EnsureHasSelectedOrganization)
    Route::get('/organizations/select', [App\Http\Controllers\OrganizationSelectionController::class, 'index'])->name('organizations.select');
    Route::get('/organizations/no-access', [App\Http\Controllers\OrganizationSelectionController::class, 'noAccess'])->name('organizations.no-access');
    
    // Organization switching
    Route::post('/organization/switch/{organization}', [OrganizationSwitchController::class, 'switch'])->name('organization.switch');
    
    // Organizations page for all authenticated users
    Route::get('/organizations', [OrganizationManagementController::class, 'userIndex'])->name('organizations.index');
    Route::get('/organizations/search', [OrganizationManagementController::class, 'search'])->name('organizations.search');
});

// Routes that require organization selection
Route::middleware(['auth', 'ensure.has.selected.organization'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Organization Cal.com test endpoint
    Route::post('/organizations/{organization}/calcom/test', [OrganizationCalcomController::class, 'test'])->name('organizations.calcom.test');
});

// Admin routes (protected by auth, admin, and organization selection middleware)
Route::middleware(['auth', 'admin', 'ensure.has.selected.organization'])->group(function () {
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
        
        // Integrations
        Route::get('/integrations', [App\Http\Controllers\IntegrationsController::class, 'index'])->name('integrations.index');
        
        // Organization Management
        Route::prefix('organizations')->name('organizations.')->group(function () {
            Route::get('/create', [OrganizationManagementController::class, 'create'])->name('create');
            Route::post('/', [OrganizationManagementController::class, 'store'])->name('store');
            Route::get('/{organization}/details', [OrganizationManagementController::class, 'details'])->name('details');
            Route::put('/{organization}', [OrganizationManagementController::class, 'update'])->name('update');
            Route::delete('/{organization}', [OrganizationManagementController::class, 'destroy'])->name('destroy');
            
            // Organization Members
            Route::get('/{organization}/members', [OrganizationManagementController::class, 'members'])->name('members');
            Route::post('/{organization}/members', [OrganizationManagementController::class, 'addMember'])->name('members.add');
            Route::put('/{organization}/members/{user}', [OrganizationManagementController::class, 'updateMemberRole'])->name('members.update');
            Route::delete('/{organization}/members/{user}', [OrganizationManagementController::class, 'removeMember'])->name('members.remove');
        });
    });
    
});

// Public invitation routes
Route::prefix('invitations')->name('invitations.')->group(function () {
    Route::get('/{token}', [App\Http\Controllers\InvitationController::class, 'show'])->name('show');
    Route::post('/{token}', [App\Http\Controllers\InvitationController::class, 'accept'])->name('accept');
});


require __DIR__.'/auth.php';
