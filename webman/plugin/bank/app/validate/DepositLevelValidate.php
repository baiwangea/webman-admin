<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 存款等级验证器
 */
class DepositLevelValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'level' => 'require|integer|gt:0',
        'name' => 'require|max:100',
        'min_amount' => 'require|float|egt:0',
        'max_amount' => 'float|egt:0',
        'rate' => 'require|float|between:0,100',
        'description' => 'max:255',
        'status' => 'integer|in:0,1',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'level.require' => '等级不能为空',
        'level.integer' => '等级必须为整数',
        'level.gt' => '等级必须大于0',
        'name.require' => '等级名称不能为空',
        'name.max' => '等级名称长度不能超过100个字符',
        'min_amount.require' => '最小金额不能为空',
        'min_amount.float' => '最小金额必须为数字',
        'min_amount.egt' => '最小金额不能小于0',
        'max_amount.float' => '最大金额必须为数字',
        'max_amount.egt' => '最大金额不能小于0',
        'rate.require' => '利率不能为空',
        'rate.float' => '利率必须为数字',
        'rate.between' => '利率必须在0-100之间',
        'description.max' => '描述长度不能超过255个字符',
        'status.integer' => '状态必须为整数',
        'status.in' => '状态值不正确',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['level', 'name', 'min_amount', 'rate'],
        'update' => ['level', 'name', 'min_amount', 'max_amount', 'rate', 'description', 'status'],
    ];
}