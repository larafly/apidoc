<?php

namespace Larafly\Apidoc\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    protected function passedValidation(): void
    {
        $data = $this->validated();

        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $propertyType = (new \ReflectionProperty($this, $key))->getType();

            if ($propertyType && $propertyType instanceof \ReflectionNamedType && !$propertyType->isBuiltin()) {
                // 如果是自定义类，如数组 DTO（例如：UserItem），构造对象数组
                $className = $propertyType->getName();

                if (is_array($value) && array_is_list($value)) {
                    $this->{$key} = array_map(fn($v) => new $className(...$v), $value);
                } else {
                    $this->{$key} = new $className(...$value);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
