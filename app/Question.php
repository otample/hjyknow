<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /*
     * 创建贴子
     * */
    public function add()
    {
        //检察用户是否登陆
        $user = user_instant()->is_login();
        if(!$user){
            return ['status'=>0,'msg'=>'login required!'];
        }

        //帖子标题
        $title = myrq('title');
        if(!$title){
            return ['status'=>1,'msg'=>'title required!'];
        }
        $this->title = $title;

        //帖子内容转译
        $desc = myrq('desc');
        if($desc){
            $this->desc = $desc;
        }

        //用户id
        $this->uid = $user;

        //存入数据库
        if( $this->save() ){
            return ['status'=>2,'msg'=>'发帖成功','id'=>$this->id];
        }else{
            return ['status'=>3,'msg'=>'发帖失败'];
        }
    }
    
    /*
     * 更新帖子
     * */
    public function change()
    {
        //获取前用户及信息帖子信息
        $uid = user_instant()->is_login();
        if(!$uid){
            return ['status'=>0,'msg'=>'请先登录'];
        }
        $qid = myrq('qid');
        $question = $this->find($qid);

        //用户ID与帖子ID是否一致
        if($uid != $question['uid']){
            return ['status'=>1,'msg'=>'没有修改权限'];
        }

        //帖子状态
        if($this->status != 0) {
            return ['status' => 2, 'msg' => '帖子不能被修改'];
        }

        //修改
        $question->title = myrq('title');
        $question->desc = myrq('desc');
        return $question->save()?
            ['status'=>3,'msg'=>'修改成功']:
            ['status'=>4,'msg'=>'修改失败'];
    }
    
    /*
     * 查看帖子
     * */
    public function search()
    {
       //有ID查询一条
        $qid = myrq('qid');
        if($qid){
            $question = $this->find($qid);
            return ['status'=>0,$question];
        }

        //没有ID,用户没有设置查询条数,默认查询10条
        $limit = myrq('limit')?myrq('limit'):10;
        $page = (myrq('page')?myrq('page'):1)-1;
        $skip = $page*$limit;
        $collection = $this
            ->orderBy('created_at')
            ->where('status','=',0)
            ->limit($limit)
            ->skip($skip)
            ->get(['id','title','status','uid','desc','created_at'])//获得类似于对象的数组,可以指定获取的字段
            ->keyBy('id')//指定某字段作为键;
        ;
        return $collection;
    }


    /*
     * 删除帖子
     * */
    public function my_delete()
    {
        //用户必须登录且为作者
        $uid = user_instant()->is_login();
        if(!$uid) {
            return ['status'=>0,'msg'=>'请先登录'];
        }

        $question = $this->find(myrq('qid'));
        if($uid != $question->uid){
            return ['status'=>1,'msg'=>'没有删除权限'];
        }
        //不是真正的删除只是将贴子状态改为隐藏
        $question->status = 1;
        $question->save();

        return  ['status'=>2,'msg'=>'删除成功'];
    }


}

