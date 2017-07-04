<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * 帖子回答
 * */
class Answer extends Model
{
    /*
     * 添加回答
     * 需要用户登录
     * questionID
     * 回答内容
     * */
    public function add()
    {
        //判断并获取当前用户id
        $this->uid = user_instant()->is_login();
        if(!$this->uid) {
            return ['status' => 0, 'msg' => '请先登录'];
        }
        //获取qid和回答内容
        $this->qid = myrq('qid');
        $this->content = myrq('cont');

        if(!$this->qid || !$this->content) {
            return ['status' => 1, 'msg' => '请选择回答的帖子,或者回答内容为空'];
        }

        //判断帖子状态
        $qinfo = question_instant()->find($this->qid);
        if(!$qinfo || !$qinfo['status'] == 0) {
            return ['status'=>2,'msg'=>'帖子不存在'];
        }

        //存入数据库
        if($this->save()) {
            return ['status' => 3, 'msg' => 'dnoe', 'aid' => $this->id];
         }
        return ['status'=>4,'msg'=>'回答失败'];
    }





    /*
     * 查看回答
     * */
    public function search()
    {
        //判断查询的是单条回答还是帖子下所有回答
        $aid = myrq('aid');
        $qid = myrq('qid');
        if($aid && !$qid){
           return $this->search_one($aid);
        }elseif(!$aid && $qid){
           return $this->search_all($qid);
        }else{
            return ['status'=>4,'msg'=>'place chose witch one do you want ,oneAnswer or allAnswers'];
        }
    }


    /*
     * 查看单条回答
     * */
    protected function search_one($aid)
    {
        //获取回答信息
        $answer = $this->find($aid);
        if($answer->status != 0){
            return ['status'=>1,'msg'=>'该回答不存在'];
        }
        return ['status'=>2,'msg'=>'获取数据成功','answer'=>$answer];
    }


    /*
     *查看所有回答
     *默认查看10条
     * */
    protected function search_all($qid)
    {
        //判断回答状态
        $qinfo = question_instant()->find($qid);
        if($qinfo->status != 0){
            return ['status'=>1,'msg'=>'该帖子已被删除'];
        }
        //获取10条该帖子的回答
        $limit = 10;
        $page = (myrq('page')?myrq('page'):1)-1;
        $skip = $page*$limit;
        $answers = $this->where(['qid'=>$qid,'status'=>0])
            ->limit($limit)
            ->skip($skip)
            ->orderBy('created_at')
            ->get()
            ->keyBy('id');
        //是否存在回答
        if(!$answers->all()){
            return ['status'=>2,'msg'=>'该帖子还没有更多回答'];
        }
        return ['status'=>3,'msg'=>'查询成功',$answers];
    }
    
    /*
     * 修改回答
     * @Parma uid aid content
     * */
    public function change(){
        //判断用户是否登陆
        $uid = user_instant()->is_login();
        if(!$uid){
            return ['status'=>0,'msg'=>'请先登录'];
        }
        
        $answer_id = myrq('aid');
        if(!$answer_id){
            return ['status'=>1,'msg'=>'请选择修改回答'];
        }
        //判断修改内容
        $content = myrq('cont');
        if(!$content){
            return ['status'=>2,'msg'=>'回答内容不能为空'];
        }
        //获取回答信息
        $old_answer = $this->find($answer_id);
        if($old_answer['status'] != 0){
            return ['status'=>3,'msg'=>'回答已删除'];
        }
        //判断修改权限
        if($old_answer['uid'] != $uid){
            return ['status'=>4,'msg'=>'没有修改权限'];
        }
        //写入数据库
        $old_answer->content = $content;
        if($old_answer->save()){
            return ['status'=>5,'msg'=>'修改成功'];
        }
        return ['status'=>6,'msg'=>'修改失败'];
    }

    /*
     * 删除回答
     * */
    public function my_delete()
    {
        //获取用户信息
        $uid = user_instant()->is_login();
        if(!$uid){
            return ['status'=>0,'msg'=>'请登录'];
        }
        //获取aid
        $aid = myrq('aid');
        if(!$aid){
            return ['status'=>1,'msg'=>'请选择要删除的回答'];
        }
        //获取回答信息
        $answer = $this->find($aid);
        if(!$answer || $answer['status'] !=0 ){
            return ['status'=>2,'msg'=>'回答不存在'];
        }
        //判断权限
        if($answer['uid'] != $uid){
            return ['status'=>3,'msg'=>'没有删除权限'];
        }
        //假删除
        $answer['status'] = 1;
        if($answer->save()){
            return ['status'=>4,'msg'=>'删除成功'];
        }
        return ['status'=>5,'msg'=>'删除失败'];
    }


    /*
     * 用户通用路由
     * */
    public function users()
    {
        return $this
            ->belongsToMany('APP\User')//指定当前模型表与APP\User多对多关系
            ->withPivot('vote')//指定中间字段
            ->withTimestamps();//自动更新中间表的时间
    }

    /*
     * 投票
     * return array
     * */
    public function vote()
    {
        //检察用户登录
        $uid = user_instant()->is_login();
        if(!$uid){
            return ['status'=>0,'msg'=>'user_login is required!'];
        }
        //获取参数
        $answer_id = myrq('aid');
        $vote = myrq('v')<=1 ? 1 : 2;
        if(!$answer_id || !$vote){
            return ['status'=>1,'msg'=>'place choose answer and vote!'];
        }
        //获取回答信息
        $answer = $this->find($answer_id);
        if(!$answer || $answer['status'] !=0 ){
            return ['status'=>2,'msg'=>'answer does not exist'];
        }

        //获取中间表中的信息
        $answer_user = $answer->users()
            ->newPivotStatement()
            ->where(['answer_id'=>$answer_id,'user_id'=>$uid])
            ->first();
        //是修改还是新建
        if($answer_user){
            if($vote == $answer_user->vote){
                return ['status'=>2,'msg'=>'you have already voted,place do not vote '.$vote.' again'];
            }
            //删除旧投票
            $answer_user->delete();
        }
        //新建新投票
       $answer->users()->attach($uid,['vote'=>$vote]);//这里laravel会自动获取中间表的另外一表的ID
        return ['status'=>3,'msg'=>'voted changed'];
    }
}
