<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Console\Command;
use Foryoufeng\Generator\Database\GeneratorSeeder;
use Foryoufeng\Generator\GeneratorServiceProvider;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apidoc:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the apidoc package';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate');
        //add default seeds
        $this->call('db:seed', ['--class' => GeneratorSeeder::class]);
        $this->info('Generator installed successfully.');
        $host = config('app.url');
        $this->info('🌐 access url: ' . route('generator.index'));
    }
}
