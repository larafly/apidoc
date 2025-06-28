<?php
namespace Larafly\Apidoc\Tests;

use App\Enums\UserTypeEnum;
use Larafly\Apidoc\Attributes\Prop;
use Larafly\Apidoc\Requests\ApiRequest;

class UserApiRequest extends ApiRequest
{
    #[Prop(desc: 'id',)]
    public int $id;

    #[Prop(desc: '名称')]
    public ?string $name;

    #[Prop(desc: '用户类型')]
    public UserTypeEnum $user_type;

    #[Prop(desc: '用户记录',type:[
        [
            'name'=>'name',
            'type'=>'?string',
            'desc'=>'用户名称',
        ],
        [
            'name'=>'age',
            'type'=>'?int',
            'desc'=>'用户年龄',
        ],
    ])]
    public ?array $user_logs;

    #[Prop(desc: '用户api',type:UserRequest::class)]
    public ?array $user_api;

}
