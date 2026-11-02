<?php

namespace App\Providers;

use App\Services\Mail\BrevoTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        if (app()->environment('production') || request()->header('X-Forwarded-Proto') === 'https' || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Mail::extend('brevo', function (array $config = []) {
            $key = $config['key'] ?? env('BREVO_API_KEY');
            return new BrevoTransport((string) $key);
        });
    }
}
