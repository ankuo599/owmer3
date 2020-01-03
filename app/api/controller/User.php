<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;

class User
{
    /**
     * 获取用户把信息
     */
    public function index()
    {
        $token = getToken();
        $data = \app\api\model\User::getByToken($token);
        returnJson('获取成功',0,$data);
    }


    public function infoByUid(int $uid){
        $data = \app\api\model\User::getByUid($uid);
        returnJson('获取成功',0,$data);
    }


    // 获取我的任务
    public function task()
    {
        $uid = getUid();
        $data = \app\api\model\Task::with([
            'user',
            'cate'
        ])
        ->field('*,FROM_UNIXTIME(create_time, "%H:%i") as create_date ')
        ->where([
            'uid'   =>  $uid
        ])
        ->select();
        returnJson('获取成功',0,$data);
    }


    // 获取我的任务
    public function changeFace()
    {
        (new My)->changeFace();
    }


}
