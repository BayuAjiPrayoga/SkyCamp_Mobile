<?php

namespace App\Providers;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\GalleryRepositoryInterface;
use App\Repositories\Contracts\KavlingRepositoryInterface;
use App\Repositories\Contracts\PeralatanRepositoryInterface;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Eloquent\GalleryRepository;
use App\Repositories\Eloquent\KavlingRepository;
use App\Repositories\Eloquent\PeralatanRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings
     */
    public array $bindings = [
        BookingRepositoryInterface::class => BookingRepository::class,
        KavlingRepositoryInterface::class => KavlingRepository::class,
        GalleryRepositoryInterface::class => GalleryRepository::class,
        PeralatanRepositoryInterface::class => PeralatanRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
