<?php

namespace Larafly\Apidoc\Tests;

use Larafly\Apidoc\Attributes\Prop;
use Larafly\Apidoc\Responses\ApiResponse;

class UserResponse extends ApiResponse
{
    #[Prop(desc: 'id')]
    public int $id;

    #[Prop(desc: '名称')]
    public string $name;

    #[Prop(desc: '完整名字')]
    public string $full_name;

    #[Prop(desc: '邮箱')]
    public string $email;

    #[Prop(desc: '创建时间')]
    public string $created_at;

    #[Prop(desc: '更新时间')]
    public string $updated_at;

    public function getDemo(): string
    {
        return <<<'json'
{
    "code": 200,
    "message": "success",
    "data": "success"
}
json;
    }
}
