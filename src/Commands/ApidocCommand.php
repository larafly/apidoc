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
        $routes = Route::getRoutes();
        $controllerInfos = collect($routes)
            ->filter(fn ($route) => isset($route->getAction()['controller']))
            ->map(function ($route) {
                $actionName = $route->getActionName();
                if (! str_contains($actionName, '@')) {
                    return null; // if not controller method route then break
                }
                [$controller, $method] = explode('@', $actionName);
                $request_method = array_filter($route->methods(), fn ($m) => in_array($m, ['GET', 'POST', 'PUT', 'DELETE']))[0] ?? '';

                return [
                    'uri' => $route->uri(),
                    'method' => $request_method,
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
                        $apiName = $apiAttr->newInstance()->name ?? '';

                        $api_methods[] = [
                            'uri' => $methodInfo['uri'],
                            'http_method' => $methodInfo['method'],
                            'method' => $methodName,
                            'method2' => $method,
                            'api_name' => $apiName,
                        ];
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
                $apidoc_type = LaraflyApidocType::firstOrNew(['alias' => $api['alias']]);
                $apidoc_type->name = $api['name'];
                dump();
                if ($apidoc_type->save()) {

                }

                return $api;
            });
        dump($controllerInfos);
    }

    public function scan()
    {
        $finder = new Finder;
        $controller_path = app_path('Http/Controllers');
        $finder->files()->in($controller_path)->name('*.php');

        foreach ($finder as $file) {
            $class = $this->getFullClassNameFromFile($file->getRealPath());

            if (! $class || ! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            // 读取类上的 Group 属性
            $groupAttributes = $reflection->getAttributes(Group::class);
            if (empty($groupAttributes)) {
                continue; // 不是你需要的 Controller
            }

            /** @var Group $groupInstance */
            $groupInstance = $groupAttributes[0]->newInstance();
            dump($groupInstance);
            //            $categoryId = $this->createOrGetCategoryByPath($groupInstance->name);

            // 遍历方法，读取 Api 属性
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $apiAttributes = $method->getAttributes(Api::class);
                if (empty($apiAttributes)) {
                    continue;
                }
                /** @var Api $apiInstance */
                $apiInstance = $apiAttributes[0]->newInstance();
                dump($apiInstance);

                //                $this->createOrUpdateApi($apiInstance->desc, $categoryId, $class, $method->getName());
            }
        }
    }

    // 根据文件路径推断完整类名（假设 PSR-4 标准）
    protected function getFullClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $content, $m)) {
            $namespace = $m[1];
            if (preg_match('/class\s+(\w+)/', $content, $m2)) {
                return $namespace.'\\'.$m2[1];
            }
        }

        return null;
    }

    // 创建或获取分类，支持多级目录
    protected function createOrGetCategoryByPath(string $path): int
    {
        $segments = explode('/', $path);
        $parentId = 0; // 顶级分类父ID为0

        $fullPath = '';
        foreach ($segments as $segment) {
            $fullPath .= $segment.'/';

            // 去除结尾 /
            $searchPath = rtrim($fullPath, '/');

            // 查询缓存或数据库是否已存在
            if (isset($this->categoryCache[$searchPath])) {
                $parentId = $this->categoryCache[$searchPath];

                continue;
            }

            // 假设 Category 是你的模型，字段有：id, name, parent_id
            $category = Category::where('name', $segment)->where('parent_id', $parentId)->first();

            if (! $category) {
                $category = Category::create([
                    'name' => $segment,
                    'parent_id' => $parentId,
                ]);
            }

            $parentId = $category->id;
            $this->categoryCache[$searchPath] = $parentId;
        }

        return $parentId; // 返回最后一级分类ID
    }

    // 创建或更新接口信息
    protected function createOrUpdateApi(string $description, int $categoryId, string $controller, string $method)
    {
        // 假设 ApiModel 是接口表模型，字段：id, category_id, controller, method, description
        $api = ApiModel::firstOrCreate([
            'category_id' => $categoryId,
            'controller' => $controller,
            'method' => $method,
        ], [
            'description' => $description,
        ]);

        // 你还可以做更新操作，比如 description 改变时更新
        if ($api->description !== $description) {
            $api->description = $description;
            $api->save();

        }
    }
}
