<?php
/**
 * Created by PhpStorm.
 * User: HuJiYang
 * Date: 2017/6/13
 * Time: 8:36
 */

/*
 * 实例化user
 * */
function user_instant()
{
    return new App\User;
}
//用户注册
Route::any('user/sin',function(){
    return user_instant()->singup();
});
//用户登录
Route::any('user/login',function(){
    return user_instant()->login();
});
//判断用户是否登陆
Route::any('user/islogin',function(){
    $user = user_instant();
    $res =  $user->is_login();
    return $res;
});
//用户退出登录
Route::any('user/logout',function(){
    return  user_instant()->logout();
});
//用户修改密码
Route::any('user/change_pwd',function(){
    return user_instant()->change_password();
});
//用户修改密码
Route::any('user/change_info',function(){
    return user_instant()->change_info();
});
//申请密码找回
Route::any('user/request_password_rest',function(){
    return user_instant()->request_password_rest();
});
//重置密码
Route::any('user/password_rest',function(){
    return user_instant()->password_rest();
});
//获取用户信息
//重置密码
Route::any('user/space',function(){
    return user_instant()->read();
});