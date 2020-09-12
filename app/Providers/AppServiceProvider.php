<?php

namespace App\Providers;

use App\Http\Handlers\PharmaciesRegistryHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PharmaciesRegistryHandler::class, function($app) {
            return new PharmaciesRegistryHandler(config('pharmacies.origin'), config('pharmacies.disk'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
