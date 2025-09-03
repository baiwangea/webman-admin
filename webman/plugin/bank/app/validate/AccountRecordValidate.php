<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 账户记录验证器
 */
class AccountRecordValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1','规则2'...]
     */
    protected $rule = [
        'user_addr' => 'require|checkAddress',
        'type' => 'require|in:deposit,withdraw,transfer_in,transfer_out,reward,fee',
        'amount' => 'require|float|gt:0',
        'balance_before' => 'float|egt:0',
        'balance_after' => 'float|egt:0',
        'tx_hash' => 'max:100',
        'remark' => 'max:500',
        'status' => 'integer|in:0,1,2',
        'related_id' => 'integer|egt:0',
        'related_type' => 'max:50',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则' => '错误信息'
     */
    protected $message = [
        'user_addr.require' => '用户地址不能为空',
        'type.require' => '记录类型不能为空',
        'type.in' => '记录类型值无效',
        'amount.require' => '金额不能为空',
        'amount.float' => '金额必须是数字',
        'amount.gt' => '金额必须大于0',
        'balance_before.float' => '变更前余额必须是数字',
        'balance_before.egt' => '变更前余额不能小于0',
        'balance_after.float' => '变更后余额必须是数字',
        'balance_after.egt' => '变更后余额不能小于0',
        'tx_hash.max' => '交易哈希不能超过100个字符',
        'remark.max' => '备注不能超过500个字符',
        'status.integer' => '状态必须是整数',
        'status.in' => '状态值只能是0、1或2',
        'related_id.integer' => '关联ID必须是整数',
        'related_id.egt' => '关联ID不能小于0',
        'related_type.max' => '关联类型不能超过50个字符',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['user_addr', 'type', 'amount', 'balance_before', 'balance_after', 'tx_hash', 'remark', 'status', 'related_id', 'related_type'],
        'update' => ['user_addr', 'type', 'amount', 'balance_before', 'balance_after', 'tx_hash', 'remark', 'status', 'related_id', 'related_type'],
        'status' => ['status'],
    ];

    /**
     * 验证用户地址格式
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    public function checkAddress($value, $rule, array $data = [], string $field = ''): bool
    {
        // 简单的地址格式验证，实际项目中应该根据具体的区块链地址格式进行验证
        if (strlen($value) < 10 || strlen($value) > 100) {
            return '用户地址格式不正确';
        }
        return true;
    }

    /**
     * 验证余额逻辑
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkBalance($value, $rule, $data)
    {
        if (isset($data['balance_before']) && isset($data['balance_after']) && isset($data['amount']) && isset($data['type'])) {
            $balanceBefore = floatval($data['balance_before']);
            $balanceAfter = floatval($data['balance_after']);
            $amount = floatval($data['amount']);
            $type = $data['type'];
            
            // 根据类型验证余额变化逻辑
            switch ($type) {
                case 'deposit':
                case 'transfer_in':
                case 'reward':
                    if ($balanceAfter !== $balanceBefore + $amount) {
                        return '余额变化不符合入账逻辑';
                    }
                    break;
                case 'withdraw':
                case 'transfer_out':
                case 'fee':
                    if ($balanceAfter !== $balanceBefore - $amount) {
                        return '余额变化不符合出账逻辑';
                    }
                    break;
            }
        }
        return true;
    }
}