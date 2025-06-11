<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Api
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
