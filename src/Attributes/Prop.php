<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Prop
{
    /**
     * @param  string  $desc  column description
     * @param  string|array  $type  column type
     */
    public function __construct(public string $desc = '', public string|array $type = '') {}
}
