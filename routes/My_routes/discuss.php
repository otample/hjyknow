<?php
/**
 * Created by PhpStorm.
 * User: HuJiYang
 * Date: 2017/6/15
 * Time: 9:58
 */

/*
 *实例化discuss对象
 *  */
function discuss_instant()
{
    return new App\Discuss;
}
//创建评论
Route::any('discuss/create',function(){
    return discuss_instant()->add();
});
//修改评论
Route::any('discuss/change',function(){
    return discuss_instant()->change();
});
//查看评论
Route::any('discuss/search',function(){
    return discuss_instant()->search();
});
//删除评论
Route::any('discuss/delete',function(){
    return discuss_instant()->my_delete();
});