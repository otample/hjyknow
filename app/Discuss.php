<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discuss extends Model
{
    /*
     * 添加评论 add
     * @pram $uid 用户ID
     * @pram $qid 帖子ID,直接评论帖子时产生
     * @pram $aid 回答ID,评论回答时产生
     * @pram $reply_to 评论ID,回复评论是产生
     * return array
     * */
    public function add()
    {
        //获取uid
        $this->uid = session('user_id');
        if(!$this->uid){
            return ['status'=>0,'msg'=>'login require!'];
        }
        //获取评论内容,判断评论类型
        $this->conment = myrq('conm');
        if(!$this->conment){
            return ['status'=>1,'msg'=>'conment require!'];
        }

        $qid = myrq('qid');
        $aid = myrq('aid');
        $reply_to = myrq('rt');
        //如果$qid $aid $reply_to三者都为空或者三者中同时存在两种,则返回选评论类型
        if( (!$qid && !$aid && !$reply_to) || ($qid && $aid) || ($aid && $reply_to) || ($qid && $reply_to)){
            return ['status'=>2,'msg'=>'choose reply type!'];
        }
        //判断帖子状态
        if($qid){
            $question_info = question_instant()->find($qid);
            if(!$question_info || $question_info->status != 0){
                return ['status'=>3,'msg'=>'question does no exists!'];
            }
            //不能自我回复
            if($question_info['uid'] == $this->uid){
                return ['status'=>8,'msg'=>'can not discusses yours question!'];
            }
            $this->qid = $qid;
            $this->level = 0;
        }
        //判断回答状态
        if($aid){
            $answer_info = answer_instant()->find($aid);
            if(!$answer_info || $answer_info->status != 0){
                return ['status'=>4,'msg'=>'answer does no exist!'];
            }
            //不能自我回复
            if($answer_info['uid'] == $this->uid){
                return ['status'=>8,'msg'=>'can not discusses yours answer!'];
            }
            $this->aid = $aid;
            $this->level = 1;
        }
        //判断评论状态
        if($reply_to){
            $reply_info = $this->find($reply_to);
            if(!$reply_info || $reply_info->status != 0){
                return ['status'=>5,'msg'=>'discuss does no exist!'];
            }
            //不能自我回复
            if($reply_info['uid'] == $this->uid){
                return ['status'=>8,'msg'=>'can not discusses yours discusses!'];
            }
            $this->reply_to = $reply_to;
            $this->level = 2;
        }
        //存入数据库
        if($this->save()){
            return ['status'=>6,'msg'=>'discuss success!','id'=>$this->id];
        }

        return ['status'=>7,'msg'=>'discuss fail!'];
    }


    /*
     * 查看评论 search
     * @pram $qid 如有,查看问题下评论
     * @pram $aid 如有,查看回答下评论
     * @pram $reply_to 如有,获取reply_to的用户及原评论信息,并返回
     * @pram $limit 每页显示评论数量
     * @pram $page 默认为0,如有则查看该页评论
     * return array
     * */
    public function search()
    {
        //判断获取的是那一个参数,qid返回问题下评论,aid返回回答下评论
        $qid = myrq('qid');
        $aid = myrq('aid');
        if( (!$qid && !$aid) || ($qid && $aid)){
            return ['status'=>0,'msg'=>'place choose discuss type!'];
        }
        //问题下的评论
        if(!$aid && $qid){
            //获取问题信息
            $question_info = discuss_instant()->find($qid);
            if(!$question_info || $question_info['status'] !=0 ){
                return ['status'=>1,'msg'=>'question does not exist!'];
            }
            //获取评论
            $discuses = $this->where(['qid'=>$qid,'status'=>0])->get()->keyBy('id');
            if(!$discuses->all()){
                return ['status'=>2,'msg'=>'this question does not have any discuses!'];
            }
            return ['status'=>3,'msg'=>'require success','discuses'=>$discuses];
        }
        //回答下的评论
        if($aid && !$qid){
            //获取回答信息
            $answer_info = answer_instant()->find($aid);
            if(!$answer_info || $answer_info['status'] !=0 ){
                return ['status'=>4,'msg'=>'answer does not exist!'];
            }
            //获取评论
            $discuses = $this->where(['aid'=>$aid,'status'=>0])->get->keyBy('id');
            if(!$discuses->all()){
                return ['status'=>5,'msg'=>'this answer does not have any discuses!'];
            }
            return ['status'=>3,'msg'=>'require success','discuses'=>$discuses];
        }
    }


    /*
     * 获取被回复的评论信息及作者
     * @pram $did 被回复评论ID
     * return array
     * */
    protected function get_replyed($did)
    {
        //获取评论信息
        $discuss_info = $this->find($did);
        if(!$discuss_info || $discuss_info['status'] != 0) {
            return ['status'=>false,'msg'=>'discuss does not exist!'];
        }
        //获取评论作者信息
        $user_info = user_instant()->find($discuss_info['uid']);
        if(!$user_info){
            return ['status'=>false,'msg'=>'user does not exist'];
        }
        return ['status'=>true,'discuss_info'=>$discuss_info,'user_name'=>$user_info['name']];
    }


    /*
     * 修改评论 change
     * @pram $uid 当前用户id
     * @pram $did 当前评论ID
     * return array
     * */

    /*
     * 删除评论 my_delete
     * @pram $uid 当前用户id
     * @pram $did 当前评论ID
     * return array
     * */
    public function my_delete()
    {
        //用户id
        $uid = session('user_id');
        $did = myrq('did');
        if(!$uid){
            return ['status'=>0,'msg'=>'place login'];
        }
        if(!$did){
            return ['status'=>1,'msg'=>'place choose discuss'];
        }
        //获取评论信息
        $discuss_info = $this->find($did);
        if($discuss_info['status'] !=0){
            return ['status'=>2,'msg'=>'discuss does not exist'];
        }
        if($uid != $discuss_info['uid']){
            return ['status'=>3,'msg'=>'do not have price'];
        }
        //判断是否有回复当前评论的评论
        $conect_discuss = $this->where(['reply_to'=>$discuss_info['id']])->get();
        if($conect_discuss && $conect_discuss->all() ){
            //循环修改所有相关联评论
            $need_change_arr = $conect_discuss->all();
            foreach($need_change_arr as $v){
                $v['status'] = 1;
                $v->save();
            }
        }
        $discuss_info->status = 1;
        if($discuss_info->save()){
            return ['status'=>4,'msg'=>'delete success'];
        }
        return ['status'=>5,'msg'=>'delete fail'];
    }

    
}
