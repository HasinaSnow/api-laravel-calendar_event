<?php

namespace App\Providers;

use App\Helpers\AboutCurrentUser;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use Illuminate\Support\ServiceProvider;

class AboutCurrentUserHelperProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AboutCurrentUser::class, function(JWTService $jWTService, VoteService $voteService){
            return new AboutCurrentUser($jWTService, $voteService);
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
