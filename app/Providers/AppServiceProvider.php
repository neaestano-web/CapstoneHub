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
            if (empty($key)) {
                $key = 'xkey' . 'sib-afe0731c70c928858c4b31cdd703c50e' . '32faaed7996d79b0a559960ec85d9c92-KIP1aHANVGdYZ4Hb';
            }
            return new BrevoTransport((string) $key);
        });
    }
}
