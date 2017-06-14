<?php

namespace App\Providers;

use App\Models\VideoModel;
use App\Models\WebgameModel;
use Illuminate\Support\ServiceProvider;

class WebGameServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('webGame', function () {
            return new WebgameModel();
        });

        $this->app->singleton('video', function () {
            return new VideoModel();
        });

    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return ['webGame', 'video'];
    }
}
