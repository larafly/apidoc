<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Api
{

    public function __construct(public string $name,public string $desc='')
    {
    }
}
