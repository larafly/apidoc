<?php

use Larafly\Apidoc\Tests\UserApiRequest;
use Larafly\Apidoc\Tests\UserLogWithUserResponse;
use Larafly\Apidoc\Tests\UserResponse;
use Larafly\Apidoc\Utils\ReflectionUtil;

it('get demo', function () {
    $demoArray = [];
    $reflection = new ReflectionClass(UserResponse::class);
    if ($reflection->hasMethod('getDemo')) {
        $method = $reflection->getMethod('getDemo');

        // Create an instance of the class
        $instance = $reflection->newInstance();

        // Call the method and get the return value
        $demoJson = $method->invoke($instance);

        // Convert JSON to array (optional)
        $demoArray = json_decode($demoJson, true);

        dump($demoArray);
    }
    expect($demoArray)->toBeArray();
});


