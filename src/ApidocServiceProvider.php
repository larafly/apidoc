<?php

namespace Larafly\Apidoc;

use Illuminate\Support\ServiceProvider;
use Larafly\Apidoc\Commands\GenerateCommand;

class ApidocServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
    }

    public function register(){

    }
}
