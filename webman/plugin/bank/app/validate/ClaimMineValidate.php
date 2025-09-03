<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 挖矿领取验证器
 */
class ClaimMineValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'user_addr' => 'require|length:42',
        'mine_id' => 'require|integer|gt:0',
        'amount' => 'require|float|gt:0',
        'status' => 'integer|in:0,1,2',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'user_addr.require' => '用户地址不能为空',
        'user_addr.length' => '用户地址长度必须为42位',
        'mine_id.require' => '挖矿ID不能为空',
        'mine_id.integer' => '挖矿ID必须为整数',
        'mine_id.gt' => '挖矿ID必须大于0',
        'amount.require' => '金额不能为空',
        'amount.float' => '金额必须为数字',
        'amount.gt' => '金额必须大于0',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值不正确',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['user_addr', 'mine_id', 'amount'],
        'update' => ['user_addr', 'mine_id', 'amount', 'status'],
    ];
}