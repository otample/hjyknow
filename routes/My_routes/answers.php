<?php
/**
 * Created by PhpStorm.
 * User: HuJiYang
 * Date: 2017/6/13
 * Time: 16:32
 */
/*
 * 实例化answers
 * */
function answer_instant()
{
    return new App\Answer;
}

//创建回复
Route::any('answer/create',function(){
    $answer = answer_instant();
    $res =  $answer->add();
    return $res;
});
//更新回复
Route::any('answer/change',function(){
    $answer = answer_instant();
    $res =  $answer->change();
    return $res;
});
//查看回复
Route::any('answer/search',function(){
    $answer = answer_instant();
    $res =  $answer->search();
    return $res;
});
//删除回复
Route::any('answer/delete',function(){
    $answer = answer_instant();
    $res =  $answer->my_delete();
    return $res;
});
//投票
Route::any('answer/vote',function(){
    return answer_instant()->vote();
});