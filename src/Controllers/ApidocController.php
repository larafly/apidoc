<?php

namespace Larafly\Apidoc\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Larafly\Apidoc\Models\LaraflyApidocType;

class ApidocController
{
    public function index(Request $request)
    {
        // The production environment does not display API documentation by default.
        if (App::isProduction() && ! config('larafly-apidoc.is_show')) {
            abort(404);
        }
        $locale = $request->get('locale') ?? config('app.locale', 'en');
        if (! in_array($locale, ['en', 'zh_CN'])) {
            $locale = 'en';
        }
        App::setLocale($locale);
        $tree = $this->buildApidocTree();

        return view('larafly-apidoc::index', compact('tree'));
    }

    public function buildApidocTree($parentId = 0): array
    {
        $types = LaraflyApidocType::where('parent_id', $parentId)->get();

        return $types->map(function ($type) {
            // Get child types recursively
            $childrenTypes = $this->buildApidocTree($type->id);

            // Get api docs under this type
            $docs = $type->larafly_apidocs->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'url' => $doc->url,
                    'creator' => $doc->creator,
                    'updater' => $doc->updater,
                    'updated_at' => $doc->updated_at->format(config('larafly-apidoc.datetime_format')),
                    'request_type' => $doc->request_type,
                    'desc' => $doc->desc ?? '',
                    'request_data' => $doc->request_data,
                    'response_data' => $doc->response_data,
                    'response_demo' => $doc->response_demo,
                ];
            })->toArray();

            // Combine types and docs
            return [
                'id' => $type->id,
                'name' => $type->name,
                'children' => array_merge($childrenTypes, $docs),
            ];
        })->toArray();
    }
}
