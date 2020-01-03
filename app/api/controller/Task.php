<?php
declare (strict_types = 1);
namespace app\api\controller;

class Task
{

    public function index()
    {
        $map = [];
        $order = 'tid';

        // 检测条件

        if( input('?sort') ){
            $trim_order = (bool)trim( input('sort') );
            $trim_order && $order = input('sort');
        }
        
        if( input('?cate') ){
            $trim_cate = (bool)trim( input('cate') );
            $trim_cate && $map['cate'] = input('cate');
        }

        $res = \app\api\model\Task::with([
            'user',
            'cate'
        ])
        ->field('*,FROM_UNIXTIME(create_time, "%H:%i") as create_date ')
        ->where($map)
        ->order($order)
        // ->order('compete')
        ->select();
        returnJson('查询成功',0,$res);
    }


    /**
     * 保存任务
     */
    public function save()
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        $data = input();
        $data['uid']  = getUid();
        $data['time'] = time();
        $data['step'] = json_encode($data['step']);

        // 验证数据
        $validate = new \app\api\validate\Task;
        $validate->check($data) || returnJson($validate->getError(),-10);

        $res = \app\api\model\Task::create($data);
        $res ? returnJson('发布成功',0,$res) : returnJson('发布失败',-10,$res) ;
    }

    /**
     * 显示任务信息
     */
    public function read(int $tid):string
    {
        $res = \app\api\model\Task::with([
            'user',
            'cate'
        ])
        ->field('*,FROM_UNIXTIME(create_time, "%H:%i") as create_date ')
        ->getByTid($tid);
        
        // 获取此任务是否收藏
        $uid = getUid();
        $favor = \app\api\model\Favor::where([
            'uid'   =>  $uid,
            'tid'   =>  $tid
        ])
        ->find();
        if($favor){
            $res['favor'] = true;
        }else{
            $res['favor'] = false;
        }

        returnJson('查询成功',0,$res);
    }


}
