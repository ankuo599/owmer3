<?php
declare (strict_types = 1);
namespace app\api\controller;

class Upload
{

    public function index(){
        request()->isPost() || returnJson('非法请求请求',-100);

        $file = request()->file('file');
        // 检测是否有上传文件
        $file || returnJson('请上传文件',-100);
        $savename = \think\facade\Filesystem::disk('public')->putFile( 'topic', $file);
        $savename = request()->domain().'/storage/'.$savename;
        returnJson('上传成功',0, $savename);
    }

}
