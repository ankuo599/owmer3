<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class Login extends Validate
{

     /**
     * 定义验证规则
     * @var array
     */	
	protected $rule = [
        'phone'             => 'require|mobile|length:11',
        'code'              =>  'require|checkCode',
    ];
    
    /**
     * 定义错误信息
     * @var array
     */	
    protected $message = [
        'code.require'                  => '验证码必须填写',
        'code.checkCode'                => '验证码错误',
        'phone.require'                 => '手机号必须填写',
        'phone.mobile'                  => '手机号格式无效',
        'phone.length'                  => '手机号长度无效',
    ];

    // 验证场景
    protected $scene = [
        'login'      =>  ['phone.mobile','phone.length','phone.require','code.require','code.checkCode'],
    ];

    // 检测验证码是否正确
    protected function checkCode(int $value,string $rule,array $data=[]):bool{

        $phone = $data['phone'];
        // 检测验证码是否过期
        (time() < cache($phone)['exp']) || returnJson('验证码已经过期',-130);

        // 检测验证码
        $code = cache($phone)['code'];
        if($data['code'] != $code){
            return false;
        }else{
            cache($phone,null);
            return true;
        }

    }


    /* --------------------- 自定义验证函数 ------------------------ */


    // 忘记密码验证
    public function forget($data){
        $this->scene('forget')->check($data) || returnJson($this->getError(),-10);
    }


}
