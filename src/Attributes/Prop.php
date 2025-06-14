<?php

namespace Larafly\Apidoc\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Prop
{
    public string $desc;//字段说明
    public string $example;//代码示例

    public function __construct(string $desc,string|int $example='')
    {
        $this->desc = $desc;
        $this->example = $example;
    }
}
