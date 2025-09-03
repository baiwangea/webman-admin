<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;
use plugin\bank\app\model\Account;

/**
 * 账户验证器
 */
class AccountValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'user_addr' => 'require|length:42|unique:'.Account::class,
        'user_id' => 'require|integer|gt:0',
        'balance' => 'require|float|egt:0',
        'status' => 'integer|in:0,1',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'user_addr.require' => '用户地址必须填写',
        'user_addr.length' => '用户地址长度必须为42位',
        'user_addr.unique' => '该用户地址已存在',
        'user_id.require' => '用户ID必须填写',
        'user_id.integer' => '用户ID必须为整数',
        'user_id.gt' => '用户ID必须大于0',
        'balance.require' => '余额必须填写',
        'balance.float' => '余额必须为数字',
        'balance.egt' => '余额不能为负数',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值只能为0或1',
    ];

    /**
     * 定义场景
     */
    protected $scene = [
        'save' => [
            'user_addr',
            'user_id',
            'balance',
            'status',
        ],
        'update' => [
            'user_addr',
            'user_id',
            'balance',
            'status',
        ],
        'balance' => [
            'balance',
        ],
        'status' => [
            'status',
        ],
    ];

    /**
     * 验证用户地址格式
     * @param mixed $value
     * @param mixed $rule
     * @param array $data
     * @return bool|string
     */
    public function checkAddress($value, $rule, array $data = [], string $field = ''): bool
    {
        if (!is_string($value) || strlen($value) !== 42) {
            return '用户地址长度必须为42位';
        }
        
        if (!str_starts_with($value, '0x')) {
            return '用户地址必须以0x开头';
        }
        
        if (!ctype_xdigit(substr($value, 2))) {
            return '用户地址格式不正确';
        }
        
        return true;
    }

    /**
     * 验证是否唯一
     * @param mixed $value
     * @param mixed $rule
     * @param array $data
     * @param string $field
     * @return bool
     */
    public function unique($value, $rule, array $data = [], string $field = ''): bool
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }

        if (str_contains($rule[0], '\\')) {
            // 指定模型类
            $db = new $rule[0];
        } else {
            return false;
        }

        $key = $rule[1] ?? $field;
        $map = [];
        $map[$key] = $value;

        if (isset($rule[2])) {
            // 排除某个主键值
            $pk = $rule[3] ?? $db->getPk();
            $map[] = [$pk, '<>', $rule[2]];
        }

        return !$db->where($map)->find();
    }
}