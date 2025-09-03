<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 存款库存验证器
 */
class DepositStockValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'deposit_id' => 'require|integer|gt:0',
        'user_addr' => 'require|length:42',
        'amount' => 'require|float|gt:0',
        'interest' => 'float|egt:0',
        'start_time' => 'require|date',
        'end_time' => 'require|date',
        'status' => 'integer|in:0,1,2',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'deposit_id.require' => '存款ID不能为空',
        'deposit_id.integer' => '存款ID必须为整数',
        'deposit_id.gt' => '存款ID必须大于0',
        'user_addr.require' => '用户地址不能为空',
        'user_addr.length' => '用户地址长度必须为42位',
        'amount.require' => '金额不能为空',
        'amount.float' => '金额必须为数字',
        'amount.gt' => '金额必须大于0',
        'interest.float' => '利息必须为数字',
        'interest.egt' => '利息不能小于0',
        'start_time.require' => '开始时间不能为空',
        'start_time.date' => '开始时间格式不正确',
        'end_time.require' => '结束时间不能为空',
        'end_time.date' => '结束时间格式不正确',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值不正确',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['deposit_id', 'user_addr', 'amount', 'start_time', 'end_time'],
        'update' => ['deposit_id', 'user_addr', 'amount', 'interest', 'start_time', 'end_time', 'status'],
    ];
}