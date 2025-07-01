<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiDocResponseCommand extends Command
{
    protected $signature = 'apidoc:response {name} {--p}';

    protected $description = 'Generate an API Response class';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $isPaginated = $this->option('p');

        $className = $isPaginated ? "{$name}PaginateResponse" : $name;
        $parentClass = $isPaginated ? 'PaginateResponse' : 'ApiResponse';
        $parentNamespace = 'Larafly\\Apidoc\\Responses\\'.$parentClass;
        $content = "<?php

namespace App\Http\Responses;

use Larafly\Apidoc\Attributes\Prop;
use {$parentNamespace};

class {$className} extends {$parentClass}
{
";

        if (! $isPaginated) {
            $content .= <<<'PHP'

    #[Prop(desc: 'id')]
    public int $id;

PHP;
        } else {
            $content .= <<<'PHP'
    #[Prop(desc: 'name')]
    public ?string $name;


PHP;
        }

        $content .= "}\n";

        $path = app_path("Http/Responses/{$className}.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("Response class [{$className}] created at {$path}");
    }
}
