<?php

namespace App\Providers;

use App\Services\Permission\Voter\AdminVoter;
use App\Services\Permission\Voter\CreateEventVoter;
use App\Services\Permission\Voter\CreateServiceUserVoter;
use App\Services\Permission\Voter\InteractEventVoter;
use App\Services\Permission\Voter\InteractServiceUserVoter;
use App\Services\Permission\Voter\InteractVoter;
use App\Services\Permission\Voter\VoteService;
use Illuminate\Support\ServiceProvider;

class VoteServiceProvider extends ServiceProvider
{

    /**
         * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(VoteService::class, function(){
            return new VoteService( [
                new AdminVoter(),
                new InteractVoter(),
                new CreateEventVoter(),
                new CreateServiceUserVoter(),
                new InteractServiceUserVoter(),
                new InteractEventVoter(),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
