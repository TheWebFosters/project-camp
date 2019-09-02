<?php

namespace App\Providers;

use App\System;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        //Share logo to login form
        View::composer(
            ['auth.login'],
            function ($view) {
                $logo = System::getValue('logo');
                $view->with('logo', $logo);
            }
        );

        //Share logo to login form
        View::composer(
            '*',
            function ($view) {
                $system_settings = System::getSystemSettings(['favicon', 'first_day_of_week']);

                $favicon = !empty($system_settings['favicon']) ? $system_settings['favicon'] : null;
                $view->with('favicon', $favicon);

                $first_day_of_week = isset($system_settings['first_day_of_week']) ? $system_settings['first_day_of_week'] : 0;
                $view->with('first_day_of_week', $first_day_of_week);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
