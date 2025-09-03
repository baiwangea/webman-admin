<?php

namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 流水记录表模型
 * Class WaterBillsRecord
 * @package plugin\bank\app\model
 */
class WaterBillsRecord extends BaseModel
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
    protected $table = 'water_bills_record';

    /**
     * 数据库连接名称
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 搜索流水获得者ID
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchTakerIdAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('taker_id', $value);
        }
        return $query;
    }

    /**
     * 搜索流水提供者ID
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchPayerIdAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('payer_id', $value);
        }
        return $query;
    }

    /**
     * 搜索盲盒记录ID
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchDepositIdAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('deposit_id', $value);
        }
        return $query;
    }

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

    /**
     * 搜索日期范围
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchDaysAttr($query, $value)
    {
        if (!empty($value)) {
            $days = intval($value);
            if ($days > 0) {
                $startTime = time() - ($days * 24 * 3600);
                $query->where('create_time', '>=', $startTime);
            }
        }
        return $query;
    }
}