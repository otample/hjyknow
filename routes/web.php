<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
 * 获取参数
 * */
function myrq($key=null,$val=null)
{
    if($key) {
        return Request::get($key, $val);
    }else{
        return Request::all();
    }
}

/*
 * 获取分页参数
 * @$page 页数
 * @$limit 每页条数
 * return array
 * */
function paging($page=1 ,$limit=15)
{
    $skip = ($page?$page-1:0)*$limit;
    return [$limit,$skip];
}

Route::get('/', function () {
    return view('index');
});
Route::get('task', function () {
    return ['a','b'];
});

//通用路由
Route::any('/timeline','CommontController@timeline');

//用户路由
require('/My_routes/users.php');
//帖子路由
require('/My_routes/questions.php');
//回答路由
require('/My_routes/answers.php');
//评论路由
require('/My_routes/discuss.php');
