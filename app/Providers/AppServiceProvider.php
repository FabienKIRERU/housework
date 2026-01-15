<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\HouseworkerRepository;
use App\Repositories\Contracts\HouseworkerRepositoryInterface;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
