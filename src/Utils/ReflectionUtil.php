<?php

namespace Larafly\Apidoc\Utils;

use Larafly\Apidoc\Attributes\Prop;
use Larafly\Apidoc\Requests\ApiRequest;
use Larafly\Apidoc\Responses\ApiResponse;
use Larafly\Apidoc\Responses\PaginateResponse;
use ReflectionClass;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;

class ReflectionUtil
{
    /**
     * get request params
     * @param string $request_class name
     * @return array
     * @throws ReflectionException
     */
    public static function request(string $request_class): array
    {
        $request_data = [];
        if (is_subclass_of($request_class, ApiRequest::class)) {
            $reflection_request = new ReflectionClass($request_class);
            foreach ($reflection_request->getProperties() as $k => $property) {
                $attributes = $property->getAttributes(Prop::class);
                if ($attributes) {
                    $request_type = $property->getType();
                    $request_name = $property->getName();

                    $request_data[$k]['name'] = $request_name;
                    if ($request_type instanceof ReflectionNamedType) {
                        $type = $request_type->getName();
                        if ($request_type->isBuiltin()) {
                            $request_data[$k]['type'] = $type;
                        } elseif (enum_exists($type)) {
                            $enumReflection = new ReflectionEnum($type);
                            $backingType = $enumReflection->getBackingType();
                            $backingTypeName = $backingType?->getName();
                            $request_data[$k]['type'] = $backingTypeName;
                        } else {
                            // if is unknown type
                            $request_data[$k]['type'] = 'string';
                        }
                        $request_data[$k]['is_required'] = ! $request_type->allowsNull();
                    }

                    foreach ($attributes as $attribute) {
                        $instance = $attribute->newInstance();
                        $request_data[$k]['desc'] = $instance->desc;
                        $prop_type = $instance->type;
                        if ($prop_type) {
                            if (is_string($prop_type)) {
                                $request_data[$k]['children'] = static::request($prop_type);
                            } elseif (is_array($prop_type)) {
                                $request_data[$k]['children'] = self::normalizeProps($prop_type);
                            }
                        }

                    }
                }

            }
        }

        return array_values($request_data);
    }


    /**
     * get response params
     * @param string $response_class name
     * @return array
     * @throws ReflectionException
     */
    public static function response(string $response_class): array
    {
        $response_data = [];
        if (is_subclass_of($response_class, ApiResponse::class)) {
            $reflection_response = new ReflectionClass($response_class);
            foreach ($reflection_response->getProperties() as $k => $property) {
                $attributes = $property->getAttributes(Prop::class);
                if ($attributes) {
                    $response_type = $property->getType();
                    $response_name = $property->getName();

                    $response_data[$k]['name'] = $response_name;
                    if ($response_type instanceof ReflectionNamedType) {
                        $type = $response_type->getName();
                        if ($response_type->isBuiltin()) {
                            $response_data[$k]['type'] = $type;
                        } elseif (enum_exists($type)) {
                            $enumReflection = new ReflectionEnum($type);
                            $backingType = $enumReflection->getBackingType();
                            $backingTypeName = $backingType?->getName();
                            $response_data[$k]['type'] = $backingTypeName;
                        }elseif (is_subclass_of($type, ApiResponse::class)){
                            $response_data[$k]['children'] = static::response($type);
                            $response_data[$k]['type'] = 'object';
                        } else {
                            // if is unknown type
                            $response_data[$k]['type'] = 'string';
                        }
                    }

                    foreach ($attributes as $attribute) {
                        $instance = $attribute->newInstance();
                        $response_data[$k]['desc'] = $instance->desc;
                        $prop_type = $instance->type;
                        if ($prop_type) {
                            if (is_string($prop_type)) {
                                $response_data[$k]['children'] = static::response($prop_type);
                            } elseif (is_array($prop_type)) {
                                $response_data[$k]['children'] = self::normalizeProps($prop_type,false);
                            }
                        }

                    }
                }

            }
        }

        return array_values($response_data);
    }

    /**
     * Handle user-defined props
     * @param array $rows need to handle data
     * @param bool $need_require is need add require row
     * @return array
     */
    public static function normalizeProps(array $rows, bool $need_require=true): array
    {

        return array_values(array_filter(array_map(function ($row) use ($need_require) {
            $requiredKeys = ['name', 'type', 'desc'];
            // Must be array
            if (! is_array($row)) {
                return null;
            }

            // Must contain all required keys
            foreach ($requiredKeys as $key) {
                if (! array_key_exists($key, $row)) {
                    return null;
                }
            }

            // Process 'type'
            $type = $row['type'];
            $isRequire = true;

            if (str_starts_with($type, '?')) {
                $isRequire = false;
                $type = substr($type, 1);
            }

            $result = [
                'name' => $row['name'],
                'type' => $type,
                'desc' => $row['desc'],
            ];

            if ($need_require) {
                $result['is_require'] = $isRequire;
            }

            return $result;
        }, $rows)));
    }

    /**
     * generate response demo
     * @param string $response_class if exist method getDemo,will return the demo
     * @param array $response_data give defined response data
     * @return array
     * @throws ReflectionException
     */
    public static function responseDemo(string $response_class,array $response_data): array
    {
        $result = [];
        if (is_subclass_of($response_class, ApiResponse::class)) {
            $method = 'getDemo';
            $reflection = new ReflectionClass($response_class);
            if ($reflection->hasMethod($method)) {
                $method = $reflection->getMethod($method);
                $instance = $reflection->newInstance();
                $demoJson = $method->invoke($instance);

                return json_decode($demoJson, true);
            }
        }

        foreach ($response_data as $field) {
            $result[$field['name']] = self::generateSampleValue($field);
        }

        return $result;
    }

    private static function generateSampleValue(array $field): mixed
    {
        $type = strtolower($field['type'] ?? 'string');
        $name = strtolower($field['name']);

        // Handle nested structures
        if (!empty($field['children']) && is_array($field['children'])) {
            if ($type === 'array') {
                return [
                    self::generateChildrenObject($field['children']),
                    self::generateChildrenObject($field['children']),
                ];
            } elseif ($type === 'object') {
                return self::generateChildrenObject($field['children']);
            }
        }

        // Generate primitive values based on name and type
        return match ($type) {
            'int', 'integer' => self::getSampleInt($name),
            'float', 'double' => 1.23,
            'bool', 'boolean' => true,
            'string' => self::getSampleString($name),
             default => null,
        };
    }

    private static function generateChildrenObject(array $children): array
    {
        $obj = [];
        foreach ($children as $child) {
            $obj[$child['name']] = self::generateSampleValue($child);
        }
        return $obj;
    }
    private static function getSampleInt(string $name): int
    {
        return match (true) {
            str_contains($name, 'code') => 200,
            str_contains($name, 'current_page') => 1,
            str_contains($name, 'last_page') => 10,
            str_contains($name, 'per_page') => 10,
            str_contains($name, 'total') => 100,
            default => 1,
        };
    }
    private static function getSampleString(string $name): string
    {
        return match (true) {
            str_contains($name, 'email') => 'test@example.com',
            str_contains($name, 'name') => 'John Doe',
            str_contains($name, 'code') => 200,
            str_contains($name, 'current_page') => 1,
            str_contains($name, 'last_page') => 10,
            str_contains($name, 'per_page') => 10,
            str_contains($name, 'total') => 100,
            str_contains($name, 'created') || str_contains($name, 'updated') => date(config('larafly-apidoc.datetime_format'),strtotime('2025-06-28 10:00:00')),
            default => 'success',
        };
    }
}
