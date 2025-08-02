<?php

namespace App\Providers;

use App\Contracts\NFLDataServiceInterface;
use App\Services\ESPN\ESPNNFLDataService;
use Illuminate\Support\ServiceProvider;

class NFLDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NFLDataServiceInterface::class, ESPNNFLDataService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
