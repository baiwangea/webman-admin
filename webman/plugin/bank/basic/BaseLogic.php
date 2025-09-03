<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\basic;

use plugin\saiadmin\basic\BaseLogic as SaiBaseLogic;

/**
 * 逻辑层基础类
 * @package plugin\bank\basic
 */
class BaseLogic extends SaiBaseLogic
{
    /**
     * @var object 模型注入
     */
    protected $model;

    /**
     * 排序字段
     * @var string
     */
    protected string $orderField = 'id';

    /**
     * 排序方式
     * @var string
     */
    protected string $orderType = 'DESC';

    /**
     * 银行插件特定的业务逻辑处理
     * @param array $data
     * @return array
     */
    protected function processBankData(array $data): array
    {
        // 银行插件特定的数据处理逻辑
        return $data;
    }

    /**
     * 格式化金额
     * @param float $amount
     * @return string
     */
    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 8, '.', '');
    }

    /**
     * 验证地址格式
     * @param string $address
     * @return bool
     */
    protected function validateAddress(string $address): bool
    {
        // 简单的地址格式验证
        return strlen($address) === 42 && str_starts_with($address, '0x');
    }
}