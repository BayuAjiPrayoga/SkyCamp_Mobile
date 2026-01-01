<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');

            // Auto-create storage link for Railway's ephemeral filesystem
            $publicStoragePath = public_path('storage');
            $storagePath = storage_path('app/public');

            if (!file_exists($publicStoragePath) && file_exists($storagePath)) {
                try {
                    symlink($storagePath, $publicStoragePath);
                } catch (\Exception $e) {
                    // Symlink failed, try copy approach or log error
                    \Log::warning('Could not create storage symlink: ' . $e->getMessage());
                }
            }
        }
    }
}
