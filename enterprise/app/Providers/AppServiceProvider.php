<?php

namespace App\Providers;

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
        view()->composer(
            ['website.webgame.layout'], 'App\Http\ViewComposers\WebGameComposer'
        );
        view()->composer(
            ['website.components.server'], 'App\Http\ViewComposers\WebGameServerComposer'
        );

        view()->composer(
            ['website.components.web.server'], 'App\Http\ViewComposers\WebServerComposer'
        );

        view()->composer(
            ['website.vrgame.layout'], 'App\Http\ViewComposers\VrGameComposer'
        );

        view()->composer(
            'website.*', 'App\Http\ViewComposers\WebSiteComposer'
        );

        view()->composer(
            ['news.components.hot'], 'App\Http\ViewComposers\HotNewsComposer'
        );

        // 需要打印sql语句，就开启以下注释

        /*DB::listen(function($sql, $bindings, $time) {
    //
    $i = 0;
    $rawSql = preg_replace_callback('/\?/', function ($matches) use ($bindings, &$i) {
    $item = isset($bindings[$i]) ? $bindings[$i] : $matches[0];
    $i++;
    return gettype($item) == 'string' ? "'$item'" : $item;
    }, $sql);
    echo $rawSql, "\n<br /><br />\n";
     */
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
