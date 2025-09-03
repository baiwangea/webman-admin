<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 自定义数据验证器
 */
class CustomizeDataValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'key' => 'require|max:100',
        'value' => 'require',
        'type' => 'require|max:50',
        'description' => 'max:255',
        'status' => 'integer|in:0,1',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'key.require' => '键名不能为空',
        'key.max' => '键名长度不能超过100个字符',
        'value.require' => '值不能为空',
        'type.require' => '类型不能为空',
        'type.max' => '类型长度不能超过50个字符',
        'description.max' => '描述长度不能超过255个字符',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值不正确',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['key', 'value', 'type'],
        'update' => ['key', 'value', 'type', 'description', 'status'],
    ];
}