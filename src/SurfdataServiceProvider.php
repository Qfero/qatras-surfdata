<?php

namespace Qatras\Surfdata;

use Illuminate\Support\ServiceProvider;
use Qatras\Surfdata\Http\Middleware\TrackProcess;


/* 
*   This class binds the package services to the project
*/
class SurfdataServiceProvider extends ServiceProvider
{

    /* 
    *   The function boot is loaded when the project boots the application
    *   @return void
    */
    public function boot()
    {
        //dd("VisitTrackServiceProvider accessed");
        $this->publishes([
            __DIR__ . '/../config/surfdata.php' => config_path('surfdata.php'),
        ]);
        // load the package routes
        $this->loadRoutesFrom( __DIR__ . '/routes/web.php' );
        // load the package views
        $this->loadViewsFrom( __DIR__ . '/resources/views', 'surfdata' );
        // load the package migrations
        $this->loadMigrationsFrom( __DIR__ . '/database/migrations' );
        // Add TrackProcess Middleware to project middlewaregroup web that runs on every page load
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', TrackProcess::class);
        
    }


    public function register()
    {
        
    }
}
