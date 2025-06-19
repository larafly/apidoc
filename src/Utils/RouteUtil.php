<?php

namespace Larafly\Apidoc\Utils;

class RouteUtil
{
    /**
     * get controller unique alias
     *
     * @return string alias
     */
    public static function getControllerAlias($controllerClass): string
    {
        // replace \ to _
        $controllerClass = str_replace(['App\\Http\\Controllers\\', '\\'], ['', '_'], $controllerClass);

        $controllerClass = preg_replace('/Controller$/', '', $controllerClass);

        return strtolower($controllerClass);
    }
}
