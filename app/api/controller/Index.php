<?php
declare (strict_types = 1);

namespace app\api\controller;
use think\facade\Db;

class Index
{
    public function index()
    {
        return 'hello api!';
    }

    public function topTask()
    {
        $res = \app\api\model\Task::with([
                'user',
                'cate'
            ])
            // ->field('*,FROM_UNIXTIME(create_time, "%Y-%m-%d %H:%i") as date ')
            ->field('*,FROM_UNIXTIME(create_time, "%H:%i") as create_date ')
            ->where([])
            ->order('compete')
            ->limit(10)
            ->select();
        returnJson('查询成功',0,$res);
    }


    public function swiper()
    {
        $data = Db::table('q_swiper')->select();
        returnJson('查询成功',0,$data);
    }


    public function cate()
    {
        $data = Db::table('q_cate')->select();
        returnJson('查询成功',0,$data);
    }

}
