<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 平台亏损验证器
 */
class PlatformLossValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'type' => 'require|max:50',
        'amount' => 'require|float|gt:0',
        'description' => 'max:255',
        'status' => 'integer|in:0,1',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'type.require' => '类型不能为空',
        'type.max' => '类型长度不能超过50个字符',
        'amount.require' => '金额不能为空',
        'amount.float' => '金额必须为数字',
        'amount.gt' => '金额必须大于0',
        'description.max' => '描述长度不能超过255个字符',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值不正确',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['type', 'amount'],
        'update' => ['type', 'amount', 'description', 'status'],
    ];
}