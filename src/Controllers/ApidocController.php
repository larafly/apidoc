<?php

namespace Larafly\Apidoc\Controllers;

use Larafly\Apidoc\Models\LaraflyApidocType;

class ApidocController
{
    public function index()
    {
        $tree = $this->buildApidocTree();

        return view('larafly-apidoc::index', compact('tree'));
    }

    public function buildApidocTree($parentId = 0): array
    {
        $types = LaraflyApidocType::where('parent_id', $parentId)->get();

        return $types->map(function ($type) {
            // Get child types recursively
            $childrenTypes = $this->buildApidocTree($type->id);

            // Get apidocs under this type
            $docs = $type->larafly_api_doc->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'url' => $doc->url,
                    'request_type' => $doc->request_type,
                    'desc' => $doc->desc ?? '',
                    'request_data' => json_decode($doc->request_data, true),
                    'response_data' => json_decode($doc->response_data, true),
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
