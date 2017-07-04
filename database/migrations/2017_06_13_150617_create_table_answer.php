<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->unsignedInteger('uid');
            $table->unsignedInteger('qid');
            $table->unsignedTinyInteger('status')->default('0')->conment('0可用,1待审核,2删除');
            $table->timestamps();

            $table->foreign('uid')->references('id')->on('users');
            $table->foreign('qid')->references('id')->on('questions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
