<?php
declare (strict_types = 1);
namespace app\api\controller;

class  Captcha
{

    /**
     * 登录
     * @route  /Captcha/getCode
     * @param  Request  $request
     * @param  string  $phone
     * @return Response
     */
    public function getCode(string $phone):void
    {
        request()->isPost() || returnJson('非法请求请求',-100);
        // 获取变量
        $data['phone'] = $phone;

        // 验证数据
        $validate = new \app\api\validate\Captcha;
        $validate->scene('login')->check($data) || returnJson($validate->getError(),-10);

        // 发送验证码
        $this->send($phone);
    }

    public function send(string $phone):string
    {

        // 判断 cache
        if( cache($phone) ){
            $sms = cache($phone);
            time() < ($sms['time']+60) && returnJson('请勿频繁发送',-120);
        }

        // 随机验证码
        $code = rand(1111, 9999);

        // 保存验证码
        cache($phone,[
            'code'  =>  $code,
            'exp'   =>  time()+600,
            'time'  =>  time()
        ]);


        // 调用短信发送接口
        
        returnJson('发送成功',0,[
            'code'  =>  $code,
            'exp'   =>  date("H:i:s",time()+600),
            'time'  =>  date("H:i:s",time())
        ]);
    }



}
