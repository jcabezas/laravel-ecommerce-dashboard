<?php

namespace App\Providers;

use App\Factories\PlatformFactory;
use App\Interfaces\ECommercePlatform;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ECommercePlatform::class, function ($app) {
            if (!Auth::check() || !Auth::user()->store) {
                return null;
            }

            $store = Auth::user()->store;
            return PlatformFactory::make($store);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
