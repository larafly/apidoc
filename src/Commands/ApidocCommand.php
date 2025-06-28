<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Larafly\Apidoc\Attributes\Api;
use Larafly\Apidoc\Attributes\Group;
use Larafly\Apidoc\Models\LaraflyApidoc;
use Larafly\Apidoc\Models\LaraflyApidocType;
use Larafly\Apidoc\Requests\ApiRequest;
use Larafly\Apidoc\Responses\ApiResponse;
use Larafly\Apidoc\Responses\PaginateResponse;
use Larafly\Apidoc\Utils\ReflectionUtil;
use Larafly\Apidoc\Utils\RouteUtil;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

class ApidocCommand extends Command
{
    public $signature = 'apidoc';

    public $description = 'generator api documents to databases';

    /**
     * @throws ReflectionException
     */
    public function handle(): int
    {
        $this->comment('start generating documents...');
        $start_time = microtime(true);
        $this->generate();
        $time = round(microtime(true) - $start_time, 2);
        $this->comment('finish generate documents with '.$time.'s...');

        return self::SUCCESS;
    }

    /**
     * generate api documentation
     *
     * @throws ReflectionException
     */
    public function generate(): void
    {
        collect(Route::getRoutes())
            ->filter(fn ($route) => isset($route->getAction()['controller']))
            ->map(fn ($route) => $this->extractRouteInfo($route))
            ->filter() // remove null
            ->groupBy('controller')
            ->map(fn ($methods, $controller) => $this->buildControllerDoc($controller, $methods))
            ->filter()
            ->values()
            ->each(fn ($api) => $this->saveControllerDoc($api));
    }

    private function extractRouteInfo($route): ?array
    {
        $actionName = $route->getActionName();
        if (! str_contains($actionName, '@')) {
            return null;
        }

        [$controller, $method] = explode('@', $actionName);
        $requestType = collect($route->methods())->first(fn ($m) => in_array($m, ['GET', 'POST', 'PUT', 'DELETE']));

        return [
            'url' => url($route->uri()),
            'request_type' => $requestType,
            'controller' => $controller,
            'controller_method' => $method,
        ];
    }

    private function buildControllerDoc(string $controller, $methods): ?array
    {
        if (! class_exists($controller)) {
            return null;
        }

        $reflection = new ReflectionClass($controller);
        $groupAttr = collect($reflection->getAttributes(Group::class))->first();
        if (! $groupAttr) {
            return null;
        }

        $groupName = $groupAttr->newInstance()->name ?? '';
        $alias = RouteUtil::getControllerAlias($controller);

        $apiMethods = collect($methods)->map(function ($info) use ($reflection) {
            return $this->buildMethodDoc($reflection, $info);
        })->filter()->values()->toArray();

        return [
            'name' => $groupName,
            'alias' => $alias,
            'controller' => $controller,
            'api_methods' => $apiMethods,
        ];
    }

    private function buildMethodDoc(ReflectionClass $reflection, array $info): ?array
    {
        $methodName = $info['controller_method'];
        if (! $reflection->hasMethod($methodName)) {
            return null;
        }

        $method = $reflection->getMethod($methodName);
        $apiAttr = collect($method->getAttributes(Api::class))->first();
        if (! $apiAttr) {
            return null;
        }

        $attr = $apiAttr->newInstance();
        if (! $attr->name) {
            return null;
        }

        $requestData = $this->resolveRequestParams($method);
        $responseData = [];
        $responseDemo = [];

        if ($returnType = $method->getReturnType()) {
            $returnName = $returnType->getName();
            $responseData = $this->getResponseData($returnName);
            $responseDemo = ReflectionUtil::responseDemo($returnName, $responseData);
        }

        return [
            'url' => $info['url'],
            'request_type' => $info['request_type'],
            'method' => $methodName,
            'desc' => $attr->desc,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'response_demo' => $responseDemo,
            'name' => $attr->name,
        ];
    }

    private function resolveRequestParams(ReflectionMethod $method): array
    {
        foreach ($method->getParameters() as $parameter) {
            $paramType = $parameter->getType();

            // Only handle named types (not union or intersection)
            if ($paramType instanceof ReflectionNamedType) {
                $requestClass = $paramType->getName();

                if (class_exists($requestClass) && is_subclass_of($requestClass, ApiRequest::class)) {
                    return ReflectionUtil::request($requestClass);
                }
            }
        }

        return [];
    }

    public function saveControllerDoc(array $api): void
    {
        $apidoc_type = $this->saveGroup($api['name'], $api['alias']);
        if (! $apidoc_type->save()) {
            return;
        }

        foreach ($api['api_methods'] as $method) {
            $record = LaraflyApidoc::firstOrNew([
                'url' => $method['url'],
            ]);

            $method['larafly_apidoc_type_id'] = $apidoc_type->id;
            $fill = [
                'larafly_apidoc_type_id' => $apidoc_type->id,
                'name' => $method['name'],
                'desc' => $method['desc'],
                'request_type' => $method['request_type'],
                'request_data' => $method['request_data'],
                'response_data' => $method['response_data'],
                'response_demo' => $method['response_demo'],
            ];
            $user = config('larafly-apidoc.author');
            if (! $record->exists) {
                $fill['creator'] = $user;
                $fill['updater'] = $user;
            } elseif ($this->canUpdate($record, $method)) {
                $fill['updater'] = $user;
            } else {
                continue;
            }

            $record->fill($fill)->save();
        }
        $this->cleanUnusedApidocTypes();
    }

    public function cleanUnusedApidocTypes(): void
    {
//        LaraflyApidocType::withCount('larafly_apidocs')
//            ->get()
//            ->filter(fn ($type) => $type->larafly_apidocs_count === 0)
//            ->each(fn ($type) => $type->delete());
        LaraflyApidocType::withCount('larafly_apidocs')
            ->get()
            ->filter(function ($type) {
                // Check if this type has no apidocs AND no children types
                $hasNoDocs = $type->larafly_apidocs_count === 0;
                $hasNoChildren = !LaraflyApidocType::where('parent_id', $type->id)->exists();

                return $hasNoDocs && $hasNoChildren;
            })
            ->each(fn ($type) => $type->delete());
    }
    /**
     * get response data by return class
     *
     * @throws ReflectionException
     */
    public function getResponseData(string $response_class): array
    {
        $response_data = [];
        if (is_subclass_of($response_class, PaginateResponse::class)) {
            $response_data = ReflectionUtil::response($response_class);
            $data = [
                [
                    'name' => 'code',
                    'type' => 'int',
                    'desc' => trans('larafly-apidoc::apidoc.response_code'),
                ],
                [
                    'name' => 'data',
                    'type' => 'array',
                    'desc' => trans('larafly-apidoc::apidoc.response_data'),
                    'children' => $response_data,
                ],
                [
                    'name' => 'message',
                    'type' => 'string',
                    'desc' => trans('larafly-apidoc::apidoc.response_message'),
                ],
                [
                    'name' => 'meta',
                    'type' => 'object',
                    'desc' => trans('larafly-apidoc::apidoc.page_meta'),
                    'children' => [
                        [
                            'name' => 'current_page',
                            'type' => 'int',
                            'desc' => trans('larafly-apidoc::apidoc.current_page'),
                        ],
                        [
                            'name' => 'last_page',
                            'type' => 'int',
                            'desc' => trans('larafly-apidoc::apidoc.last_page'),
                        ],
                        [
                            'name' => 'per_page',
                            'type' => 'int',
                            'desc' => trans('larafly-apidoc::apidoc.per_page'),
                        ],
                        [
                            'name' => 'total',
                            'type' => 'int',
                            'desc' => trans('larafly-apidoc::apidoc.page_total'),
                        ],
                    ],
                ],
            ];
            $response_data = $data;
        } elseif (is_a($response_class, ApiResponse::class, true)) {
            $response_data = ReflectionUtil::response($response_class);
            $data = [
                [
                    'name' => 'code',
                    'type' => 'int',
                    'desc' => trans('larafly-apidoc::apidoc.response_code'),
                ],
                [
                    'name' => 'data',
                    'type' => 'object',
                    'desc' => trans('larafly-apidoc::apidoc.response_data'),
                    'children' => $response_data,
                ],
                [
                    'name' => 'message',
                    'type' => 'string',
                    'desc' => trans('larafly-apidoc::apidoc.response_message'),
                ],
            ];
            $response_data = $data;
        }

        return $response_data;
    }

    /**
     * judge data has changed
     */
    private function canUpdate(LaraflyApidoc $apiDoc, array $method): bool
    {
        return $apiDoc->name !== $method['name']
              || $apiDoc->desc !== $method['desc']
              || $apiDoc->url !== $method['url']
              || $apiDoc->larafly_apidoc_type_id !== $method['larafly_apidoc_type_id']
              || $apiDoc->request_type !== $method['request_type']
              || $apiDoc->request_data !== $method['request_data']
              || $apiDoc->response_demo !== $method['response_demo']
              || $apiDoc->response_data !== $method['response_data'];
    }

    /**
     * save api doc type
     */
    private function saveGroup(string $group, string $alias): LaraflyApidocType
    {
        $segments = explode('/', $group);
        $parentId = 0;
        $type = null;

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            // Build unique alias path like: "parent", "parent_child", etc.
            $fullAlias = $parentId === 0 ? $alias : $alias.'_'.$segment;

            // Save or update node
            $type = LaraflyApidocType::updateOrCreate(
                ['alias' => $fullAlias],
                [
                    'name' => $segment,
                    'parent_id' => $parentId,
                ]
            );

            $parentId = $type->id;
        }

        return $type; // Return the deepest (last) level
    }
}
