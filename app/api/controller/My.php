<?php
declare (strict_types = 1);
namespace app\api\controller;

class My
{

    public function index(){
        request()->isPost() || returnJson('非法请求请求',-100);
    }

    // 修改昵称
    public function changeNickname(string $nickname):string
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        $uid = getUid();
        // 保存更新
        $data = \app\api\model\User::where([
            'uid'   =>  $uid
        ])
        ->update([
            'nickname'  =>  $nickname
        ]);
        returnJson('修改成功',0,$nickname);

    }

    // 更改资料
    public function changeInfo():string
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        $data = input();

        if( isset($data['birthday']) ){
            $data['age'] = getDiffYear($data['birthday']);
        }


        $uid = getUid();
        // 保存更新
        $res = \app\api\model\User::where([
            'uid'   =>  $uid
        ])
        ->update($data);
        returnJson('修改成功',0,$res);

    }

    // 获取我的任务
    public function changeFace()
    {
        request()->isPost() || returnJson('非法请求请求',-100);

        $file = request()->file('file');
        // 检测是否有上传文件
        $file || returnJson('请上传文件',-100);

        $uid = getUid();

        // 删除旧的头像
        $user = \app\api\model\User::getByUid($uid);
        $face = str_replace( request()->domain(),"",$user['face']);
        is_file($face) && unlink($face);

        $savename = \think\facade\Filesystem::disk('public')->putFile( 'topic', $file);
        $savename = request()->domain().'/storage/'.$savename;
        // 保存更新
        $data = \app\api\model\User::where([
            'uid'   =>  $uid
        ])
        ->update([
            'face'  =>  $savename
        ]);
        returnJson('修改成功',0,$savename);
    }

}
