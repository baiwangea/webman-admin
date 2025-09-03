<?php

namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 平台亏损表模型
 * Class PlatformLoss
 * @package plugin\bank\app\model
 */
class PlatformLoss extends BaseModel
{
    /**
     * 主键
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 表名
     * @var string
     */
    protected $table = 'platform_loss';

    /**
     * 数据库连接名称
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 搜索金额范围
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchAmountRangeAttr($query, $value)
    {
        if (!empty($value)) {
            $range = explode(',', $value);
            if (count($range) == 2) {
                $query->whereBetween('amount', [$range[0], $range[1]]);
            }
        }
        return $query;
    }
}