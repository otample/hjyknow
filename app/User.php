<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;
use DB;


class User extends Model
{
    public $get_username = null;
    public $get_password = null;
    /*
     * 用户注册
     * */
    public function singup()
    {
        //用户名及密码
        $user_info = $this->get_user_info();
        if(!$user_info){
            return ['status'=>0,'msg'=>'user_name and password are both required!'];
        }
        $username = $this->get_username;
        $password = $this->get_password;
        //用户名是否存在
        $user_exist = $this->where('name',$username)->exists();
        if($user_exist){
            return ['status'=>0,'msg'=>'user already exist!'];
        }

        //加密密码
        $hashed_password = Hash::make($password);
//        dd($hashed_password);
        //存入对象
        $this->name = $username;
        $this->password = $hashed_password;
        //存入数据库
        if($this->save()){
            return ['status'=>1,'msg'=>'singup success!'];
        }else{
            return ['status'=>0,'msg'=>'singup fail!'];
        }
    }


    /*
     * 用户登录
     * parameter username
     * parameter password
     * */
    public function login()
    {
        //用户名及密码
        $user_info = $this->get_user_info();
        if(!$user_info){
            return ['status'=>0,'msg'=>'user_name and password are both required!'];
        }
        $username = $this->get_username;
        $password = $this->get_password;
        //获取数据库用户信息
        $user = $this->where('name',$username)->first();
        //检察用户是否存在
        if($user){
            //判断密码是否符合
            if( Hash::check($password,$user->password) ){
                //将用户信息写入session
                session()->put('user_name',$user->name);
                session()->put('user_id',$user->id);
                return ['status'=>2,'msg'=>'login success!'];
            }else{
                return ['status'=>3,'msg'=>'wrong password!'];
            }
        }else{
            return ['status'=>1,'msg'=>'username does no exist!'];
        }

    }

    /*
     * 用户退出登录
     *
     * */
    public function logout()
    {
        //判断用户是否登陆
        if($this->is_login()){
            //清除用户信息,跳转到首页
            session()->forget('user_id');
            session()->forget('user_name');
            return redirect('/');
        }else{
            return 2;
        }
    }

    /*
     * 判断用户是否登陆
     * return:user_id或者false
     * */
    public function is_login()
    {
        return session('user_id')?session('user_id'):false;
    }

    /*
     * 获取用户信息
     * */
    public function get_user_info()
    {
        $username = Request::get('username');
        $password = Request::get('pwd');

        if($username && $password){
            $this->get_username = $username;
            $this->get_password = $password;
            return true;
        }else{
            return false;
        }
    }
    

    /*
     *修改用户密码
     * 1判断用户是否登陆
     * 2输入原密码
     * 执行数据库修改
     * */
    public function change_password()
    {
        //获取用户id
        $uid = $this->is_login();
        if(!$uid){
            return ['status'=>0,'msg'=>'user login required!'];
        }
        //获取新及原始密码
        $new_password = myrq('npwd');
        $pwd = myrq('pwd');
        if(!$new_password || !$pwd){
            return ['status'=>1,'msg'=>'new password and old password are both required!'];
        }
        //判断原始密码是否符合
//        DB::enableQueryLog();
        $user_info = $this->find($uid);
//        $log = DB::getQueryLog();
//        dd($log);
        $old_password = $user_info->password;
        if(!Hash::check($pwd,$old_password)){
            return ['status'=>2,'msg'=>'old password does not right!'];
        }
        //判断新密码是否变动
        if(Hash::check($new_password,$old_password)){
            return ['status'=>3,'msg'=>'password has no changing'];
        }
        //修改密码
        $user_info->password = Hash::make($new_password);
        if( $user_info->save() ){
            $this->logout();
            return ['status'=>4,'msg'=>'your password has changed , place relogin !'];
        }
        return ['status'=>5,'msg'=>'change password fail!'];
    }
    

    /*
     * 修改除密码外的其他信息
     * 用户需要登录
     * 传值即为要修改的选项
     * */
    public function change_info()
    {
        $uid = $this->is_login();
        if(!$uid){
            return ['status'=>0,'msg'=>'user login required!'];
        }
        //是否有需要修改的信息
        $new_name = myrq('nn');
        $new_email = myrq('ne');
        if(!$new_name && !$new_email){
            return ['status'=>1,'msg'=>'place choose witch information that you need to change!'];
        }
        //获取用户信息
        $user_info = $this->find($uid);
        if(($new_name == $user_info->name) && ($new_email == $user_info->email)){
            return ['status'=>2,'msg'=>'no information need to change!'];
        }
        //判断具体修改何种信息
        if($new_name != $user_info->name){
            $user_info->name = $new_name;
        }
        if($new_email != $user_info->email){
            $user_info->email = $new_email;
        }
        //更新到数据库
        if(!$user_info->save()){
            return ['status'=>3,'msg'=>'information changing fail!'];
        }
        return ['status'=>4,'msg'=>'information changing success!'];
    }


    /*
     * 申请密码找回
     * 获取手机号
     * 返回验证码到数据库,同时发送给第三方
     * 第三方返回数据
     * */
    public function request_password_rest()
    {
        //判断上次发送时间
        $last_send_time = session('send_time');
        $resend_time = time() - $last_send_time;
        if($resend_time < 60){
            return ['status'=>4,'msg'=>'you can only require checking once in one minute!'];
        }
        //获取手机号
        $phone = myrq('phone');
        if(!$phone){
            return ['status'=>0,'msg'=>'phone is required!'];
        }
        //数据库中查询
        $user = $this->where('phone',$phone)->first();
        if(!$user){
            return ['status'=>1,'msg'=>'phone does no exist!'];
        }

        //获取接口数据
        $user->phone_proving = rand(1001,9999);
        if(!$user->save()){
            return ['status'=>2,'msg'=>'phone_proving sending  fail! try again'];
        }
        //将此次操作时间存入session
        $send_time = time();
        session()->put('send_time',$send_time);
        //调用第三方接口,发送短信
        return ['status'=>3,'msg'=>'phone_proving sending success! continue'];
    }


    /*
     *重置密码
     * */
    public function password_rest()
    {
        //获取参数
        $phone = myrq('phone');
        $proving = myrq('proving');
        if(!$phone || !$proving){
            return ['status'=>0,'msg'=>'phone and proving are both required!'];
        }
        //获取用户信息
        DB::enableQueryLog();
        $user = $this->where(['phone'=>$phone,'phone_proving'=>$proving])->first();
        dd(DB::getQueryLog());
        if(!$user){
            return ['status'=>1,'msg'=>'phone or proving is wrong!'];
        }
        //重新生成密码并返回
        $new_password = rand(100000,999999);
        $user->password = bcrypt($new_password);
        //存入数据库,并返回
        if(!$user->save()){
            return ['status'=>2,'msg'=>'whoops something is wrong try again!'];
        }
        return ['status'=>3,'msg'=>'reset password success ,new password is '.$new_password];
    }

    /*
     *获取用户信息(用户空间)
     * 用户名,用户手机号,邮箱
     * 用户发布的问题(分页)
     * 用户回答的问题
     *  */
    public function read()
    {
       //获取uid
        $uid = myrq('uid')?myrq('uid'):$this->is_login();//这里有待商榷
        $get_info = ['name','email','phone','created_at'];
        $user = $this->find($uid,$get_info);
        if(!$user){
            return ['status'=>0,'msg'=>'user does not exist!'];
        }
        //用户回答的答案
        $answers = answer_instant()
            ->where(['uid'=>$uid,'status'=>0])
            ->orderByDesc('created_at')
            ->get();
//        dd($answers->toArray());
        //用户发布的问题
        $questions = question_instant()
            ->where(['uid' => $uid, 'status' => 0])
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'desc']);
        $userspace_info = ['uses'=>$user,'answers'=>$answers,'questions'=>$questions];
        return ['status'=>1,'msg'=>'query ok','info'=>$userspace_info];
    }


    /*
     * 用户链接回答表通用路由
     * */
    public function answers()
    {
        return $this
            ->belongsToMany('APP\Answer')//指定当前模型表与APP\User多对多关系
            ->withPivot('vote')//指定中间字段
            ->withTimestamps();//自动更新中间表的时间
    }
}
