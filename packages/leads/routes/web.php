<?php

use Illuminate\Support\Facades\Route;
use CkCrm\Leads\Http\Controllers\LeadController;

Route::middleware(config('leads.routes.middleware', ['web', 'auth', 'admin']))
    ->prefix(config('leads.routes.prefix', 'leads'))
    ->as(config('leads.routes.as', 'leads.'))
    ->group(function () {
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::get('/create', [LeadController::class, 'create'])->name('create');
        Route::post('/', [LeadController::class, 'store'])->name('store');
        Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
        Route::get('/{lead}/edit', [LeadController::class, 'edit'])->name('edit');
        Route::put('/{lead}', [LeadController::class, 'update'])->name('update');
        
        if (config('leads.features.archive', true)) {
            Route::post('/{lead}/archive', [LeadController::class, 'archive'])->name('archive');
        }
        
        if (config('leads.features.bulk_actions', true)) {
            Route::post('/bulk-archive', [LeadController::class, 'bulkArchive'])->name('bulk-archive');
        }
    });