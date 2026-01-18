<?php

namespace App\Providers;

use App\Repositories\ServiceRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\HouseworkerRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\HouseworkerRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(
            HouseworkerRepositoryInterface::class, HouseworkerRepository::class
        );
        $this->app->bind(
            ServiceRepositoryInterface::class, ServiceRepository::class
        );

        $this->app->bind(
            ReservationRepositoryInterface::class, ReservationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
