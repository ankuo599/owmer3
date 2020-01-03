<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class Chat extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'to_id'         =>  'require',
        'from_id'       =>  'require',
        'from_face'     =>  'require',
        'type'          =>  'require',
        'data'          =>  'require',
        'client_id'     =>  'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'to_id.require'       => 'to_id必须填写',
        'from_id.require'     => 'from_id必须填写',
        'from_face.require'   => 'from_face必须填写',
        'type.require'        => 'type必须填写',
        'data.require'        => 'data必须填写',
        'client_id.require'   => 'data必须填写',
    ];

    protected $scene = [
        'send'=>['to_id','from_face','type','data'],
        'bind'=>['client_id'],
    ];


}
