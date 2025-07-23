<?php

namespace App\Providers;

use App\Mail\Transport\ResendTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Resend\Client;

class ResendServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('resend', function (array $config = []) {
            $apiKey = $config['key'] ?? config('services.resend.key');
            
            if (empty($apiKey)) {
                throw new \InvalidArgumentException('Resend API key is not configured.');
            }
            
            // Create the client using the factory method
            $client = \Resend::client($apiKey);
            
            // Don't pass Laravel's event dispatcher or logger as they don't implement PSR interfaces
            // The transport will work without them
            return new ResendTransport($client);
        });
    }
}