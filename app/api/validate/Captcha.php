<?php
declare (strict_types = 1);

namespace app\api\validate;
use think\Validate;

class Captcha extends Validate
{
    
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     * @var array
     */	
	protected $rule = [
        'phone'         => 'require|mobile|length:11'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     * @var array
     */	
    protected $message = [
        'phone.require'         => '手机号必须填写',
        'phone.mobile'          => '手机号格式无效',
        'phone.length'          => '手机号长度无效'
    ];

    // 检测 code 码
    protected function checkCode($value,$rule,$data=[]){

    }

    // 验证场景
    protected $scene = [
        // 'sms'  =>  ['phone'],
    ];      


}
