<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Prop
{
    /**
     * @param  string  $name  column name
     * @param  string  $desc  column description
     * @param  string|int  $example  column demo
     * @param  string|array  $type  column type
     */
    public function __construct(public string $desc = '', public string|int $example = '', public string|array $type = '') {}
}
