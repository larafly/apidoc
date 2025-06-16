<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Console\Command;

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
        $this->info('Apidoc installed successfully.');
        $this->info('🌐 access url: '.route('larafly-apidoc.index'));
    }
}
