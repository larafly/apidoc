<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Support\Facades\Storage;

class ApidocMdCommand extends ApidocCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'apidoc:md';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'generator api documents to markdown files';

    #[\Override]
    public function saveControllerDoc(array $api): void
    {
        $groupName = $api['name'];
        $module = $api['module'];
        $folder = "apidoc/{$module}/{$groupName}";

        foreach ($api['api_methods'] as $method) {
            $markdown = $this->buildMarkdownDoc($method);
            $fileName = $method['name'].'.md';
            Storage::disk('public')->put("$folder/{$fileName}", $markdown);
        }
    }

    private function buildMarkdownDoc(array $method): string
    {
        $lines = [];

        // Title
        $lines[] = '# '.$method['name'];
        $lines[] = '';
        if ($method['desc']) {
            $lines[] = '* '.$method['desc'];
        }
        $lines[] = '';

        // URL
        $lines[] = '### '.trans('larafly-apidoc::apidoc.request_url');
        $lines[] = '';
        $lines[] = '* '.$method['url'];
        $lines[] = '';

        // Method
        $lines[] = '### '.trans('larafly-apidoc::apidoc.request_type');
        $lines[] = '';
        $lines[] = '* '.$method['request_type'];
        $lines[] = '';

        // Request Params
        $lines[] = '### '.trans('larafly-apidoc::apidoc.request_param');
        $lines[] = '';
        $lines[] = '| '.trans('larafly-apidoc::apidoc.name').' | '.trans('larafly-apidoc::apidoc.is_required').' | '.trans('larafly-apidoc::apidoc.type').' | '.trans('larafly-apidoc::apidoc.desc').' |';
        $lines[] = '|:------|:------|:------|:------|';

        foreach ($method['request_data'] ?? [] as $item) {
            $lines[] = sprintf(
                '| %s | %s | %s | %s |',
                $item['name'] ?? '',
                ! empty($item['is_required']) ? 'Y' : 'N',
                $item['type'] ?? '',
                $item['desc'] ?? ''
            );
        }

        $lines[] = '';

        // Response Params
        $lines[] = '### '.trans('larafly-apidoc::apidoc.response_param');
        $lines[] = '';
        $lines[] = '| '.trans('larafly-apidoc::apidoc.name').' | '.trans('larafly-apidoc::apidoc.type').' | '.trans('larafly-apidoc::apidoc.desc').' |';
        $lines[] = '|:------|:------|:------|';

        foreach ($method['response_data'] ?? [] as $item) {
            $lines[] = sprintf(
                '| %s | %s | %s |',
                $item['name'] ?? '',
                $item['type'] ?? '',
                $item['desc'] ?? ''
            );
        }

        $lines[] = '';

        // Response Demo
        $lines[] = '### '.trans('larafly-apidoc::apidoc.response_demo');
        $lines[] = '';
        $lines[] = '```json';
        $lines[] = json_encode($method['response_demo'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $lines[] = '```';

        return implode("\n", $lines);
    }
}
