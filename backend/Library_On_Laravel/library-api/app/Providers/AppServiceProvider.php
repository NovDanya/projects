<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Messaging\MessagingInterface;
use App\Services\Messaging\Telegram\Methods;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MessagingInterface::class, Methods::class);
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
