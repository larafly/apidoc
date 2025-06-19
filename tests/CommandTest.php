<?php

uses(Tests\TestCase::class);

use Illuminate\Support\Facades\Artisan;

it('call apidoc', function () {
    $res = Artisan::call('apidoc');
    dump($res);
    expect($res)->toBe(0);

});

it('call apidoc install', function () {
    $res = Artisan::call('apidoc:install');
    expect($res)->toBe(0);
});
