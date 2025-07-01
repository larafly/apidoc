<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiDocRequestCommand extends Command
{
    protected $signature = 'apidoc:request {name} {--p}';

    protected $description = 'Generate an API Request class';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $isPaginated = $this->option('p');

        $className = $name;
        $parentClass = $isPaginated ? 'PageApiRequest' : 'ApiRequest';
        $parentNamespace = 'Larafly\\Apidoc\\Requests\\'.$parentClass;
        $content = "<?php

namespace App\Http\Requests;

use Larafly\Apidoc\Attributes\Prop;
use {$parentNamespace};

class {$className} extends {$parentClass}
{
";

        if (! $isPaginated) {
            $content .= <<<'PHP'

    #[Prop(desc: 'id')]
    public int $id;

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'id is required',
        ];
    }

PHP;
        } else {
            $content .= <<<'PHP'
    #[Prop(desc: 'name')]
    public ?string $name;


PHP;
        }

        $content .= "}\n";

        $path = app_path("Http/Requests/{$className}.php");
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        $this->info("Request class [{$className}] created at {$path}");
    }
}
