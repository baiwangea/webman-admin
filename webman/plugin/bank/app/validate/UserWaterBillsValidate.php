<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 用户流水账单验证器
 */
class UserWaterBillsValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'user_addr' => 'require|length:42',
        'type' => 'require|max:50',
        'amount' => 'require|float|gt:0',
        'balance_before' => 'float|egt:0',
        'balance_after' => 'float|egt:0',
        'description' => 'max:255',
        'status' => 'integer|in:0,1',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'user_addr.require' => '用户地址不能为空',
        'user_addr.length' => '用户地址长度必须为42位',
        'type.require' => '类型不能为空',
        'type.max' => '类型长度不能超过50个字符',
        'amount.require' => '金额不能为空',
        'amount.float' => '金额必须为数字',
        'amount.gt' => '金额必须大于0',
        'balance_before.float' => '变动前余额必须为数字',
        'balance_before.egt' => '变动前余额不能小于0',
        'balance_after.float' => '变动后余额必须为数字',
        'balance_after.egt' => '变动后余额不能小于0',
        'description.max' => '描述长度不能超过255个字符',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值不正确',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['user_addr', 'type', 'amount'],
        'update' => ['user_addr', 'type', 'amount', 'balance_before', 'balance_after', 'description', 'status'],
    ];
}