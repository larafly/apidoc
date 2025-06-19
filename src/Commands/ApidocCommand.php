<?php

namespace Larafly\Apidoc\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Larafly\Apidoc\Attributes\Api;
use Larafly\Apidoc\Attributes\Group;
use Larafly\Apidoc\Models\LaraflyApidocType;
use Larafly\Apidoc\Utils\RouteUtil;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder;

class ApidocCommand extends Command
{
    public $signature = 'apidoc';

    public $description = 'laravel api docs generator';

    public function handle(): int
    {
        $this->comment('All done');
        //        $this->scan();
        $this->getInfo();

        return self::SUCCESS;
    }

    public function getInfo()
    {
            collect(Route::getRoutes())
            ->filter(fn ($route) => isset($route->getAction()['controller']))
            ->map(function ($route) {
                $actionName = $route->getActionName();
                if (! str_contains($actionName, '@')) {
                    return null; // if not controller method route then break
                }
                [$controller, $method] = explode('@', $actionName);
                $request_type = array_filter($route->methods(), fn ($m) => in_array($m, ['GET', 'POST', 'PUT', 'DELETE']))[0] ?? '';

                return [
                    'url' => url($route->uri()),
                    'request_type' => $request_type,
                    'controller' => $controller,
                    'controller_method' => $method,
                ];
            })
            ->groupBy('controller')  // 按 controller 分组，方便处理
            ->map(function ($methods, $controller) {
                if (! class_exists($controller)) {
                    return null;
                }
                $reflection = new ReflectionClass($controller);

                // if this class class contains #[Group]
                $groupAttr = collect($reflection->getAttributes(Group::class))->first();
                if (! $groupAttr) {
                    return null;
                }

                $groupName = $groupAttr->newInstance()->name ?? '';
                $api_methods = [];
                $alias = RouteUtil::getControllerAlias($controller);

                foreach ($methods as $methodInfo) {
                    $methodName = $methodInfo['controller_method'];
                    if (! $reflection->hasMethod($methodName)) {
                        continue;
                    }

                    $method = $reflection->getMethod($methodName);
                    $apiAttr = collect($method->getAttributes(Api::class))->first();
                    if ($apiAttr) {
                        $attr = $apiAttr->newInstance();
                        $name = $attr->name;
                        if($name){
                            dump($attr);
                            $api_methods[] = [
                                'url' => $methodInfo['url'],
                                'request_type' => $methodInfo['request_type'],
                                'method' => $methodName,
                                'desc' => $attr->desc,
                                'request_data' => json_encode($methodInfo),
                                'response_data' => json_encode($methodInfo),
//                            'method2' => $method,
                                'name' => $name,
                            ];
                        }


                    }

                }

                return [
                    'name' => $groupName,
                    'alias' => $alias,
                    'controller' => $controller,
                    'api_methods' => $api_methods,
                ];
            })
            ->filter() // 去除没有 Group 的控制器
            ->values()->map(function ($api) {
                $apidoc_type = $this->saveGroup($api['name'],$api['alias']);
                if ($apidoc_type->save()) {
                    foreach ($api['api_methods'] as $method) {
                          $apidoc_type->larafly_api_doc()->updateOrCreate(
                                ['url' => $method['url']], // Unique key
                                [
                                    'name' => $method['name'],
                                    'desc' => $method['desc'],
                                    'request_type' => $method['request_type'],
                                    'request_data' => $method['request_data'],
                                    'response_data' => $method['response_data'],
                                ]
                            );
                    }
                }

                return $api;
            });
    }

    private function saveGroup(string $group,string $alias):LaraflyApidocType{
        $segments = explode('/', $group);
        $parentId = 0;
        $type = null;

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            // Build unique alias path like: "parent", "parent_child", etc.
            $fullAlias = $parentId===0 ? $alias : $alias . '_' . $segment;

            // Save or update node
            $type = LaraflyApidocType::updateOrCreate(
                ['alias' => $fullAlias],
                [
                    'name' => $segment,
                    'parent_id' => $parentId,
                ]
            );

            // Set parent for next level
            $parentId = $type->id;
        }

        return $type; // Return the deepest (last) level
    }

}
