<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Group
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
