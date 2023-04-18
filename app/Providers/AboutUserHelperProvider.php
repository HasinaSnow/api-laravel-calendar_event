<?php

namespace App\Providers;

use App\Helpers\AboutUser;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use Illuminate\Support\ServiceProvider;

class AboutUserHelperProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AboutUser::class, function(JWTService $jWTService, VoteService $voteService){
            return new AboutUser($jWTService, $voteService);
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
