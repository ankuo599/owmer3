<?php
declare (strict_types = 1);
namespace app\api\controller;

class Favor
{
    /**
     * 我的收藏列表
     */
    public function index():string
    {
        $uid = getUid();
        // 保存更新
        $res = \app\api\model\Favor::alias('a')
        ->field('b.*,c.nickname,c.face,c.phone,d.name as cate_name,FROM_UNIXTIME(a.create_time, "%H:%i") as create_date ')
        ->where(['a.uid'   =>  $uid])
        ->join('task b','a.tid = b.tid')
        ->join('user c','b.uid = c.uid')
        ->join('cate d','b.cate = d.cid')
        ->select();
        returnJson('查询成功',0,$res);
    }

    // 添加收藏
    public function add():string
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        $data = input();
        $data['uid'] = getUid();
        $data['create_time'] = time();

        // 保存更新
        $res = \app\api\model\Favor::create($data);
        returnJson('收藏成功',0,$res);
    }

    // 取消收藏
    public function delete():string
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        $data = input();
        $data['uid'] = getUid();

        // 删除
        $res = \app\api\model\Favor::where([
            'uid'   =>  $data['uid'],
            'tid'   =>  $data['tid']
        ])
        ->delete();
        returnJson('取消成功',0,$res);
    }

    
}
