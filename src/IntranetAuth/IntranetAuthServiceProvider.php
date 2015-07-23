<?php

namespace Ghi\IntranetAuth;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class IntranetAuthServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @param Repository $config
     */
    public function boot(Repository $config)
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'ghi');

        $this->publishes([
            __DIR__ . '/../views' => base_path('resources/views/vendor/ghi')
        ]);

        $model = $config->get('auth.model');

        Auth::extend('ghi-intranet', function($app) use($model) {
            return new IntranetUserAuthProvider($model);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
