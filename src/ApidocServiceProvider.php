<?php

namespace Larafly\Apidoc;

use Illuminate\Support\ServiceProvider;
use Larafly\Apidoc\Commands\ApidocCommand;
use Larafly\Apidoc\Commands\InstallCommand;

class ApidocServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Publishing the views.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'larafly-apidoc');

        // the language
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'larafly-apidoc');

        // load migrations
        $this->loadMigrationsFrom(__DIR__.'/../resources/migrations');

        // Publishing apidoc files.
        $this->publishes([
            __DIR__.'/../config/larafly-apidoc.php' => config_path('larafly-apidoc.php'),
        ], 'larafly-apidoc');

        // routes
        $this->loadRoutesFrom(__DIR__.'/../routes/route.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApidocCommand::class,
                InstallCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/larafly-apidoc.php', 'larafly-apidoc'
        );
    }
}
