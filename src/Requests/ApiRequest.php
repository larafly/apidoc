<?php

namespace Larafly\Apidoc\Requests;

use Illuminate\Foundation\Http\FormRequest;
use ReflectionNamedType;
use ReflectionProperty;

abstract class ApiRequest extends FormRequest
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
                // If it's a custom class, construct an array of objects.
                $className = $propertyType->getName();
                if (enum_exists($className)) {
                    // if type is  enum，use ::from to set key
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
