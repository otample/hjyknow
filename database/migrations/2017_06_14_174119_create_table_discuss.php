<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscuss extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discusses', function (Blueprint $table) {
            $table->increments('id');
            $table->text('conment')->comment('评论内容');
            $table->unsignedInteger('qid')->nullable()->comment('帖子ID');
            $table->unsignedInteger('uid')->comment('用户ID');
            $table->unsignedInteger('aid')->nullable()->comment('回答ID');
            $table->unsignedInteger('reply_to')->nullable()->conmment('原评论ID');
            $table->unsignedTinyInteger('status')->default(0)->conmment('评论状态,0已发布,1未发布,2已删除');
            $table->unsignedTinyInteger('level')->default(0)->comment('评论等级,0:评论问题,1:评论回答,2:评论评论');
            $table->timestamps();

            $table->foreign('qid')->references('id')->on('questions');
            $table->foreign('uid')->references('id')->on('users');
            $table->foreign('aid')->references('id')->on('answers');
            $table->foreign('reply_to')->references('id')->on('discusses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discusses');
    }
}
