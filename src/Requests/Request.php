<?php

namespace Larafly\Apidoc\Requests;

use Illuminate\Foundation\Http\FormRequest;
use ReflectionNamedType;
use ReflectionProperty;

abstract class Request extends FormRequest
{
    protected function passedValidation(): void
    {
        $data = $this->validated();

        foreach ($data as $key => $value) {
            if (! property_exists($this, $key)) {
                continue;
            }

            $propertyType = (new ReflectionProperty($this, $key))->getType();

            if ($propertyType instanceof ReflectionNamedType && ! $propertyType->isBuiltin()) {
                // 如果是自定义类，构造对象数组
                $className = $propertyType->getName();
                if (enum_exists($className)) {
                    // 是 enum，使用 ::from 构造
                    $this->{$key} = $className::from($value);
                } elseif (is_array($value) && array_is_list($value)) {
                    $this->{$key} = array_map(fn ($v) => new $className(...$v), $value);
                } else {
                    $this->{$key} = new $className(...$value);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
