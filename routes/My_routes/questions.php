<?php
/**
 * Created by PhpStorm.
 * User: HuJiYang
 * Date: 2017/6/13
 * Time: 8:40
 */
/*
 * 实例化question
 * */
function question_instant()
{
    return new App\Question;
}

//创建帖子
Route::any('question/create',function(){
    $question = question_instant();
    $res =  $question->add();
    return $res;
});
//更新帖子
Route::any('question/change',function(){
    $question = question_instant();
    $res =  $question->change();
    return $res;
});
//查看帖子
Route::any('question/search',function(){
    $question = question_instant();
    $res =  $question->search();
    return $res;
});
//删除帖子
Route::any('question/delete',function(){
    $question = question_instant();
    $res =  $question->my_delete();
    return $res;
});