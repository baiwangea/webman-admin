<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 订单验证器
 */
class OrdersValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1','规则2'...]
     */
    protected $rule = [
        'user_addr' => 'require|checkAddress',
        'order_no' => 'max:50|unique:orders',
        'type' => 'require|in:deposit,withdraw,transfer,mine,reward',
        'amount' => 'require|float|gt:0',
        'status' => 'integer|in:0,1,2,3',
        'remark' => 'max:500',
        'tx_hash' => 'max:100',
        'fee' => 'float|egt:0',
        'complete_time' => 'integer|egt:0',
        'related_id' => 'integer|egt:0',
        'related_type' => 'max:50',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则' => '错误信息'
     */
    protected $message = [
        'user_addr.require' => '用户地址不能为空',
        'order_no.max' => '订单号不能超过50个字符',
        'order_no.unique' => '订单号已存在',
        'type.require' => '订单类型不能为空',
        'type.in' => '订单类型值无效',
        'amount.require' => '订单金额不能为空',
        'amount.float' => '订单金额必须是数字',
        'amount.gt' => '订单金额必须大于0',
        'status.integer' => '状态必须是整数',
        'status.in' => '状态值只能是0、1、2或3',
        'remark.max' => '备注不能超过500个字符',
        'tx_hash.max' => '交易哈希不能超过100个字符',
        'fee.float' => '手续费必须是数字',
        'fee.egt' => '手续费不能小于0',
        'complete_time.integer' => '完成时间必须是整数',
        'complete_time.egt' => '完成时间不能小于0',
        'related_id.integer' => '关联ID必须是整数',
        'related_id.egt' => '关联ID不能小于0',
        'related_type.max' => '关联类型不能超过50个字符',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['user_addr', 'order_no', 'type', 'amount', 'status', 'remark', 'tx_hash', 'fee', 'related_id', 'related_type'],
        'update' => ['user_addr', 'order_no', 'type', 'amount', 'status', 'remark', 'tx_hash', 'fee', 'complete_time', 'related_id', 'related_type'],
        'create' => ['user_addr', 'type', 'amount', 'remark'],
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
     * 验证字段唯一性
     * @param $value
     * @param $rule
     * @param $data
     * @param $field
     * @return bool|string
     */
    public function unique($value, $rule, array $data = [], string $field = ''): bool
    {
        $model = new \plugin\bank\app\model\Orders();
        $query = $model->where($field, $value);
        
        // 如果是更新操作，排除当前记录
        if (isset($data['id']) && $data['id']) {
            $query->where('id', '<>', $data['id']);
        }
        
        $exists = $query->find();
        return $exists ? false : true;
    }

    /**
     * 验证订单金额
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    public function checkAmount($value, $rule, array $data = [], string $field = ''): bool
    {
        $amount = floatval($value);
        
        // 根据订单类型验证金额范围
        if (isset($data['type'])) {
            switch ($data['type']) {
                case 'deposit':
                    if ($amount < 10) {
                        return '存款金额不能少于10';
                    }
                    break;
                case 'withdraw':
                    if ($amount < 1) {
                        return '提现金额不能少于1';
                    }
                    break;
                case 'transfer':
                    if ($amount < 0.1) {
                        return '转账金额不能少于0.1';
                    }
                    break;
            }
        }
        
        return true;
    }
}