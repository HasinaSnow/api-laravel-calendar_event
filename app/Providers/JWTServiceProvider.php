<?php

namespace App\Providers;

use App\Services\JWT\JWTService;
use Illuminate\Support\ServiceProvider;

class JWTServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(JWTService::class, function($app){
            return new JWTService(
                env('SECRET'), 
                env('_ALGO'),
                env('_HEADER'),
                env('_BEARER'),
                env('_EXP')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
