<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 存款验证器
 */
class DepositValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1','规则2'...]
     */
    protected $rule = [
        'user_addr' => 'require|checkAddress',
        'amount' => 'require|float|gt:0',
        'level_id' => 'require|integer|gt:0',
        'order_no' => 'max:50|unique:deposit',
        'tx_hash' => 'max:100',
        'status' => 'integer|in:0,1,2',
        'remark' => 'max:500',
        'confirm_time' => 'integer|egt:0',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则' => '错误信息'
     */
    protected $message = [
        'user_addr.require' => '用户地址不能为空',
        'amount.require' => '存款金额不能为空',
        'amount.float' => '存款金额必须是数字',
        'amount.gt' => '存款金额必须大于0',
        'level_id.require' => '存款等级不能为空',
        'level_id.integer' => '存款等级必须是整数',
        'level_id.gt' => '存款等级必须大于0',
        'order_no.max' => '订单号不能超过50个字符',
        'order_no.unique' => '订单号已存在',
        'tx_hash.max' => '交易哈希不能超过100个字符',
        'status.integer' => '状态必须是整数',
        'status.in' => '状态值只能是0、1或2',
        'remark.max' => '备注不能超过500个字符',
        'confirm_time.integer' => '确认时间必须是整数',
        'confirm_time.egt' => '确认时间不能小于0',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['user_addr', 'amount', 'level_id', 'order_no', 'tx_hash', 'status', 'remark'],
        'update' => ['user_addr', 'amount', 'level_id', 'order_no', 'tx_hash', 'status', 'remark', 'confirm_time'],
        'create' => ['user_addr', 'amount', 'level_id'],
        'confirm' => ['tx_hash'],
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
        $model = new \plugin\bank\app\model\Deposit();
        $query = $model->where($field, $value);
        
        // 如果是更新操作，排除当前记录
        if (isset($data['id']) && $data['id']) {
            $query->where('id', '<>', $data['id']);
        }
        
        $exists = $query->find();
        return $exists ? false : true;
    }
}