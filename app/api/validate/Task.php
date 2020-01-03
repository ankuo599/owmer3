<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class Task extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'uid'           => 'require',
        'title'         => 'require',
        'cate'          => 'require',
        'device'        => 'require',
        'explain'       => 'require',
        'finish_time'   => 'require',
        'audit_time'    => 'require',
        'price'         => 'require|min:0.1',
        'number'        => 'require|min:10|integer',
        'step'          => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'uid.require'         => 'uid必须填写',
        'title.require'       => '项目名称必须填写',
        'cate.require'        => '请选择类型',
        'device.require'      => '请选择支持设备',
        'explain.require'     => '项目说明必须填写',
        'price.require'       => '价格必须填写',
        'price.min'           => '价格不能小于0.1',
        'number.require'      => '数量必须填写',
        'number.min'          => '数量不能小于10',
        'number.integer'      => '数量类型不对',
        'step.require'        => '步骤必须填写',
    ];
    
}
