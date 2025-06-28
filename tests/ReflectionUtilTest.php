<?php

use Larafly\Apidoc\Tests\UserApiRequest;
use Larafly\Apidoc\Tests\UserLogWithUserResponse;
use Larafly\Apidoc\Tests\UserResponse;
use Larafly\Apidoc\Utils\ReflectionUtil;

it('request', function () {

    $res = ReflectionUtil::request(UserApiRequest::class);
    dump($res);
    expect($res)->toBeArray();
});

it('response', function () {
    $res = ReflectionUtil::response(UserResponse::class);
    dump($res);
    expect($res)->toBeArray();
});

it('response with object', function () {
    $res = ReflectionUtil::response(UserLogWithUserResponse::class);
    dump($res);
    expect($res)->toBeArray();
});
