<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('parent_id')->comment('parent id')->default(0);
            $table->string('module')->comment('module');
            $table->unique(['name', 'parent_id', 'module'], 'name_module_unique');
            $table->timestamps();
        });
        Schema::create('larafly_apidocs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('larafly_apidoc_type_id')->comment('apidoc type id');
            $table->string('creator')->comment('create user');
            $table->string('updater')->comment('update user');
            $table->string('name')->comment('name');
            $table->string('desc')->nullable()->comment('api description');
            $table->string('request_type')->comment('request type');
            $table->string('url')->unique()->comment('url');
            $table->json('request_data')->comment('request data');
            $table->json('response_data')->comment('response data');
            $table->json('response_demo')->comment('response demo');
            $table->timestamps();

            $table->foreign('larafly_apidoc_type_id')->references('id')->on('larafly_apidoc_types')->onDelete('cascade');
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
