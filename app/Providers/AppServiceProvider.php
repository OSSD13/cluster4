<?php

namespace App\Providers;

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
        if (config('app.env') === 'production') {
            // Force custom base path (เช่น app อยู่ใน subfolder หรือใช้ domain เฉพาะ)
            $customRootUrl = config('app.url'); // หรือใช้ env('FORCE_URL') ถ้ามี
            URL::forceRootUrl($customRootUrl);
            // Force HTTPS
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
