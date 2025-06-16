<?php

use Larafly\Apidoc\Controllers\ApidocController;
use Illuminate\Support\Facades\Route;
use Larafly\Apidoc\Controllers\AssetController;

Route::prefix(config('larafly-apidoc.route'))->group(function () {
    Route::get('/', [ApidocController::class, 'index'])->name('larafly-apidoc.index');

});
Route::get('larafly-apidoc/assets/{path}', AssetController::class)->where('path', '.*');

