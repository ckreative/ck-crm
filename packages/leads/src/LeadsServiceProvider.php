<?php

namespace CkCrm\Leads;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use CkCrm\Leads\Console\Commands\SyncCalcomBookings;
use CkCrm\Leads\Models\Lead;
use CkCrm\Leads\Policies\LeadPolicy;
use Illuminate\Support\Facades\Gate;

class LeadsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/leads.php', 'leads'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'leads');
        
        // Only load routes if enabled
        if (config('leads.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncCalcomBookings::class,
            ]);

            // Publish config
            $this->publishes([
                __DIR__.'/config/leads.php' => config_path('leads.php'),
            ], 'leads-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'leads-migrations');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/leads'),
            ], 'leads-views');
        }

        // Register policies
        Gate::policy(Lead::class, LeadPolicy::class);

        // Register scheduler
        $this->callAfterResolving('Illuminate\Console\Scheduling\Schedule', function ($schedule) {
            // Run sync for all organizations with Cal.com enabled
            $schedule->command('leads:sync-calcom --all')->everyFifteenMinutes();
        });

        // Share navigation data with views
        $this->app['view']->composer('*', function ($view) {
            $view->with('leadsNavigationEnabled', config('leads.navigation.enabled', true));
            
            // Generate route with organization parameter if available
            $currentOrg = app()->has('current_organization') ? app('current_organization') : null;
            if ($currentOrg) {
                $view->with('leadsNavigationRoute', route('leads.index', ['organization' => $currentOrg->slug]));
            } else {
                $view->with('leadsNavigationRoute', '#');
            }
        });
    }
}