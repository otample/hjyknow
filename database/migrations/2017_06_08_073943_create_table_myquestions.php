<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMyquestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',64)->comment('标题');
            $table->text('desc')->nullable()->comment('问题描述');
            $table->unsignedInteger('uid')->comment('users表主键');
            $table->unsignedTinyInteger('status')->default('0')->comment('帖子状态:0已发布1未发布2被禁');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
