<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 挖矿验证器
 */
class MineValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1','规则2'...]
     */
    protected $rule = [
        'name' => 'require|max:100',
        'type' => 'require|in:fixed,float,compound',
        'rate' => 'require|float|gt:0|lt:100',
        'duration' => 'integer|egt:1',
        'min_amount' => 'require|float|gt:0',
        'max_amount' => 'float|gt:0',
        'description' => 'max:1000',
        'status' => 'integer|in:0,1',
        'sort' => 'integer|egt:0',
        'start_time' => 'integer|egt:0',
        'end_time' => 'integer|egt:0|checkTimeRange',
        'total_limit' => 'float|egt:0',
        'user_limit' => 'float|egt:0',
        'risk_level' => 'integer|in:1,2,3,4,5',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则' => '错误信息'
     */
    protected $message = [
        'name.require' => '挖矿项目名称不能为空',
        'name.max' => '挖矿项目名称不能超过100个字符',
        'type.require' => '挖矿类型不能为空',
        'type.in' => '挖矿类型值无效',
        'rate.require' => '收益率不能为空',
        'rate.float' => '收益率必须是数字',
        'rate.gt' => '收益率必须大于0',
        'rate.lt' => '收益率必须小于100',
        'duration.integer' => '挖矿周期必须是整数',
        'duration.egt' => '挖矿周期不能小于1天',
        'min_amount.require' => '最小投资金额不能为空',
        'min_amount.float' => '最小投资金额必须是数字',
        'min_amount.gt' => '最小投资金额必须大于0',
        'max_amount.float' => '最大投资金额必须是数字',
        'max_amount.gt' => '最大投资金额必须大于0',
        'description.max' => '项目描述不能超过1000个字符',
        'status.integer' => '状态必须是整数',
        'status.in' => '状态值只能是0或1',
        'sort.integer' => '排序必须是整数',
        'sort.egt' => '排序不能小于0',
        'start_time.integer' => '开始时间必须是整数',
        'start_time.egt' => '开始时间不能小于0',
        'end_time.integer' => '结束时间必须是整数',
        'end_time.egt' => '结束时间不能小于0',
        'total_limit.float' => '总限额必须是数字',
        'total_limit.egt' => '总限额不能小于0',
        'user_limit.float' => '用户限额必须是数字',
        'user_limit.egt' => '用户限额不能小于0',
        'risk_level.integer' => '风险等级必须是整数',
        'risk_level.in' => '风险等级值只能是1-5',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['name', 'type', 'rate', 'duration', 'min_amount', 'max_amount', 'description', 'status', 'sort', 'start_time', 'end_time', 'total_limit', 'user_limit', 'risk_level'],
        'update' => ['name', 'type', 'rate', 'duration', 'min_amount', 'max_amount', 'description', 'status', 'sort', 'start_time', 'end_time', 'total_limit', 'user_limit', 'risk_level'],
        'create' => ['name', 'type', 'rate', 'min_amount', 'description'],
        'status' => ['status'],
    ];

    /**
     * 验证时间范围
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkTimeRange($value, $rule, $data)
    {
        if (isset($data['start_time']) && $data['start_time'] > 0 && $value > 0) {
            if ($value <= $data['start_time']) {
                return '结束时间必须大于开始时间';
            }
        }
        return true;
    }

    /**
     * 验证金额范围
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkAmountRange($value, $rule, $data)
    {
        if (isset($data['min_amount']) && $data['min_amount'] > 0) {
            if ($value <= $data['min_amount']) {
                return '最大投资金额必须大于最小投资金额';
            }
        }
        return true;
    }

    /**
     * 验证收益率合理性
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkRate($value, $rule, $data)
    {
        $rate = floatval($value);
        
        // 根据挖矿类型验证收益率范围
        if (isset($data['type'])) {
            switch ($data['type']) {
                case 'fixed':
                    if ($rate > 50) {
                        return '固定收益率不能超过50%';
                    }
                    break;
                case 'float':
                    if ($rate > 80) {
                        return '浮动收益率不能超过80%';
                    }
                    break;
                case 'compound':
                    if ($rate > 30) {
                        return '复利收益率不能超过30%';
                    }
                    break;
            }
        }
        
        return true;
    }

    /**
     * 验证挖矿周期
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkDuration($value, $rule, $data)
    {
        $duration = intval($value);
        
        // 根据挖矿类型验证周期范围
        if (isset($data['type'])) {
            switch ($data['type']) {
                case 'fixed':
                    if ($duration < 1 || $duration > 365) {
                        return '固定挖矿周期必须在1-365天之间';
                    }
                    break;
                case 'float':
                    if ($duration < 1 || $duration > 30) {
                        return '浮动挖矿周期必须在1-30天之间';
                    }
                    break;
                case 'compound':
                    if ($duration < 7 || $duration > 180) {
                        return '复利挖矿周期必须在7-180天之间';
                    }
                    break;
            }
        }
        
        return true;
    }
}