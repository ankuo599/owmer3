<?php
declare (strict_types = 1);
namespace app\api\controller;
use think\facade\Db;

class Swiper
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $data = Db::table('q_swiper')->select();
        returnJson('查询成功',0,$data);
    }

}
