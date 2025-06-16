<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('larafly_apidoc_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('name');
            $table->string('alias')->unique()->comment('alias');
            $table->timestamps();
        });
        Schema::create('larafly_apidocs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('larafly_apidoc_type_id')->comment('apidoc type id');
            $table->string('name')->comment('name');
            $table->string('request_type')->comment('request type');
            $table->string('url')->comment('url');
            $table->json('request_data')->comment('request data');
            $table->json('response_data')->comment('response data');
            $table->timestamps();

            $table->foreign('larafly_apidoc_type_id')->references('id')->on('laravel_apidoc_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('larafly_apidocs');
        Schema::dropIfExists('larafly_apidoc_types');
    }
};
