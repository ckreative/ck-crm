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

Route::middleware('auth')->group(function () {
    // Organization selection routes (not protected by EnsureHasSelectedOrganization)
    Route::get('/organizations/select', [App\Http\Controllers\OrganizationSelectionController::class, 'index'])->name('organizations.select');
    Route::get('/organizations/no-access', [App\Http\Controllers\OrganizationSelectionController::class, 'noAccess'])->name('organizations.no-access');
    
    // Organization switching
    Route::post('/organization/switch/{organization}', [OrganizationSwitchController::class, 'switch'])->name('organization.switch');
    
    // Organizations page for all authenticated users
    Route::get('/organizations', [OrganizationManagementController::class, 'userIndex'])->name('organizations.index');
    Route::get('/organizations/search', [OrganizationManagementController::class, 'search'])->name('organizations.search');
    
    // Global profile routes (not organization-scoped)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Favicon routes (no auth required for favicons)
Route::prefix('{organization}')->group(function () {
    Route::get('/favicon.ico', [App\Http\Controllers\FaviconController::class, 'favicon'])->name('organization.favicon');
    Route::get('/favicon-{size}.png', [App\Http\Controllers\FaviconController::class, 'favicon'])->name('organization.favicon.sized');
});

// Organization-scoped routes
Route::middleware(['auth', 'organization.from.url'])->prefix('{organization}')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Organization Cal.com test endpoint
    Route::post('/calcom/test', [OrganizationCalcomController::class, 'test'])->name('organizations.calcom.test');
    
    // Admin routes for organization context
    Route::middleware('admin')->group(function () {
        // Leads routes (from package)
        Route::prefix('leads')->name('leads.')->group(function () {
            Route::get('/', [CkCrm\Leads\Http\Controllers\LeadController::class, 'index'])->name('index');
            Route::get('/create', [CkCrm\Leads\Http\Controllers\LeadController::class, 'create'])->name('create');
            Route::post('/', [CkCrm\Leads\Http\Controllers\LeadController::class, 'store'])->name('store');
            Route::get('/{lead}', [CkCrm\Leads\Http\Controllers\LeadController::class, 'show'])->name('show');
            Route::get('/{lead}/edit', [CkCrm\Leads\Http\Controllers\LeadController::class, 'edit'])->name('edit');
            Route::put('/{lead}', [CkCrm\Leads\Http\Controllers\LeadController::class, 'update'])->name('update');
            Route::post('/{lead}/archive', [CkCrm\Leads\Http\Controllers\LeadController::class, 'archive'])->name('archive');
            Route::post('/bulk-archive', [CkCrm\Leads\Http\Controllers\LeadController::class, 'bulkArchive'])->name('bulk-archive');
        });
        
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
        });
    });
});

// Admin routes (global, not organization-scoped)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('app-settings')->name('app-settings.')->group(function () {
        // Organization Management (global admin routes)
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
