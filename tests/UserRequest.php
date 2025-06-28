<?php
namespace Larafly\Apidoc\Tests;

use App\Enums\UserTypeEnum;
use Larafly\Apidoc\Attributes\Prop;
use Larafly\Apidoc\Requests\ApiRequest;

class UserRequest extends ApiRequest
{
    #[Prop(desc: 'id',)]
    public int $id;

    #[Prop(desc: '名称')]
    public ?string $name;

    #[Prop(desc: '用户类型')]
    public UserTypeEnum $user_type;

}
