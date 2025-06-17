<?php

namespace Larafly\Apidoc\Utils;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteUtil
{
    /**
     * use controller and method to return route info
     * @param  string  $controllerClass
     * @param  string  $methodName
     * @return array   route info
     */
    public static function getRoute(string $controllerClass, string $methodName): array
    {
        $matchedRoute = [];

        foreach (Route::getRoutes() as $route) {
            $action = $route->getAction();

            if (isset($action['controller'])) {
                [$class, $method] = Str::parseCallback($action['controller']);

                if ($class === $controllerClass && $method === $methodName) {
                    $uri = $route->uri();
                    $method = array_filter($route->methods(), fn($m) => in_array($m, ['GET', 'POST', 'PUT', 'DELETE']))[0]??'';
                    $matchedRoute = [
                        'uri' => $uri,
                        'method' => $method,
                        'name' => $route->getName(),
                        'full_url' => self::getFullRouteUrl($uri),
                    ];
                }
            }
        }

        return $matchedRoute;
    }

    /**
     * get full url
     * @param  string  $uri
     * @return string  full url
     */
    public static function getFullRouteUrl(string $uri): string
    {
        $appUrl = rtrim(config('app.url'), '/');

        return $appUrl . '/' . ltrim($uri, '/');
    }

    public static function getControllerAlias($controllerClass): string
    {
        // remove namespace
        $controllerClass = str_replace('App\\Http\\Controllers\\', '', $controllerClass);

        // replace \ to _
        $controllerClass = str_replace('\\', '_', $controllerClass);

        // remove Controller
        $controllerClass = preg_replace('/Controller$/', '', $controllerClass);

        // translate to lower
        return strtolower($controllerClass);
    }

}
