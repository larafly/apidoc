<?php

namespace Larafly\Apidoc\Requests;

use Larafly\Apidoc\Attributes\Prop;

class PageApiRequest extends ApiRequest
{
    #[Prop(desc: '页码,最小为1')]
    public ?int $page = 1;

    #[Prop(desc: '每页条数,最小为10，最大为100')]
    public ?int $per_page = 10;

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'page.min' => '页码必须大于1',
            'per_page.min' => '每页数必须大于10',
            'per_page.max' => '每页数不能超过100',
        ];
    }
}
