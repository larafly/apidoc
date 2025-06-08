<?php

namespace Larafly\ApiDoc\Commands;

use Illuminate\Console\Command;

class ApiDocCommand extends Command
{
    public $signature = 'apidoc';

    public $description = 'laravel api docs generator';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
