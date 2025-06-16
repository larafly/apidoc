<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Group
{
    public function __construct(public string $name)
    {
    }
}
