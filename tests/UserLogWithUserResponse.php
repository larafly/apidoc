<?php

namespace Larafly\Apidoc\Tests;

use Larafly\Apidoc\Attributes\Prop;
use Larafly\Apidoc\Responses\ApiResponse;

class UserLogWithUserResponse extends ApiResponse
{
    #[Prop(desc: 'id')]
    public int $id;

    #[Prop(desc: '名称')]
    public string $name;

    #[Prop(desc: '用户信息')]
    public UserResponse $user;
}
