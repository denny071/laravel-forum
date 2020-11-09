<?php

namespace App\Providers;

use App\Models\Channel;
use Carbon\Carbon;
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
        Carbon::setLocale('zh');
        // view composer
        \view()->composer("*", function ($view) {
            $channels = \Cache::rememberForever('channels', function () {
                return Channel::all();
            });
            $view->with("channels",$channels);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
             $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
