<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;
use plugin\bank\app\model\Claim;

/**
 * 领取验证器
 */
class ClaimValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'user_addr' => 'require|length:42|checkAddress',
        'type' => 'require|in:daily,invite,task,bonus',
        'amount' => 'require|float|gt:0',
        'order_no' => 'unique:'.Claim::class,
        'status' => 'integer|in:0,1,2',
        'tx_hash' => 'length:66',
        'remark' => 'max:500',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'user_addr.require' => '用户地址必须填写',
        'user_addr.length' => '用户地址长度必须为42位',
        'user_addr.checkAddress' => '用户地址格式不正确',
        'type.require' => '领取类型必须填写',
        'type.in' => '领取类型只能为daily,invite,task,bonus',
        'amount.require' => '领取金额必须填写',
        'amount.float' => '领取金额必须为数字',
        'amount.gt' => '领取金额必须大于0',
        'order_no.unique' => '订单号已存在',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值只能为0,1,2',
        'tx_hash.length' => '交易哈希长度必须为66位',
        'remark.max' => '备注最多不能超过500个字符',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'save' => [
            'user_addr',
            'type',
            'amount',
        ],
        'update' => [
            'status',
            'remark',
            'tx_hash',
        ],
        'create' => [
            'user_addr',
            'type',
            'amount',
        ],
        'process' => [
            'status',
            'remark',
            'tx_hash',
        ],
    ];
}