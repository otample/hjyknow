<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommontController extends Controller
{
    //测试
    public function timeline()
    {
        //获取分页信息
        list($limit,$skip) = paging(myrq('p'));
        //获取指定页数中的已发布问题
        $questions = question_instant()
            ->where(['status'=>0])
            ->limit($limit)
            ->skip($skip)
            ->orderBy('created_at','desc')
            ->get();

        //获取指定页数中的已发布问题
        $answers = answer_instant()
            ->where(['status'=>0])
            ->limit($limit)
            ->skip($skip)
            ->orderBy('created_at','desc')
            ->get();
//    dd($answers);
        //合并数据(必须是两条数据表有外键?)

        $data = $questions->merge($answers);
        $data = $data->sortByDesc(function($items){
            return $items->created_at;
        })->values()->all();
//        dd($data);
        return $data;
//        return $answers;
//        return $questions;
    }
}
