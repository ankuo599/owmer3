<?php
declare (strict_types = 1);
namespace app\api\controller;

class Login
{
    /**
     * 登录
     * @route  /login/index
     * @param  Request  $request
     * @param  string  $phone
     * @param  int  $code
     * @return Response
     */
    public function index(string $phone,int $code):string
    {
        request()->isPost() || returnJson('非法请求请求',-100);

        // 验证数据
        $validate = new \app\api\validate\Login;
        // $validate->check(input()) || returnJson($validate->getError(),-10);

        // 生成token
        $token = app_token();
        // 判断手机号是否注册 没有注册就默认注册
        $user = \app\api\model\User::getByPhone($phone);
        if(!$user){
            $user = \app\api\model\User::create([
                'phone'         =>  $phone,
                'nickname'      =>  uniqid(),
                'token'         =>  $token['token'],
                'create_time'   =>  time()
            ]);
        }else{
                // 把token绑定给新用户
            \app\api\model\User::where([
                'phone' =>  $phone
            ])->update([
                'token' =>  $token['token']
            ]);
        }

        $user['token']  = $token;
        returnJson('登录成功',0,$user);
    }
  
    
    /**
     * 获取token
     * @route  /login/getToken
     * @param  Request  $request
     * @param  string  $token
     * @return Response
     */
    public function getToken(string $token=''):string
    {
        // 检测header 头部是否有token
        if( request()->header('token') ){
            $token = request()->header('token');
        }else{
            // 获取token
            $token = input('token');
            $token || returnJson('缺少token',-500);
        }
        $user = \app\api\model\User::getByToken($token);
        // 没有查到用户说明是无效的token
        $user || returnJson('无效的token',-10);

        // 获取新的token
        $data = get_token($token);

        // 把token绑定给新用户
        \app\api\model\User::where([
            'token' =>  $token
        ])->update([
            'token' =>  $data['token']
        ]);

        returnJson('获取成功',0,$data);
    }


}
