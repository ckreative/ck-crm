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
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

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
            if (config('leads.calcom.sync_enabled', false)) {
                $schedule->command('leads:sync-calcom')->everyFifteenMinutes();
            }
        });

        // Share navigation data with views
        $this->app['view']->composer('*', function ($view) {
            $view->with('leadsNavigationEnabled', config('leads.navigation.enabled', true));
            $view->with('leadsNavigationRoute', route(config('leads.routes.prefix', 'leads') . '.index'));
        });
    }
}