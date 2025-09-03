<?php

namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 用户流水表模型
 * Class UserWaterBills
 * @package plugin\bank\app\model
 */
class UserWaterBills extends BaseModel
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
    protected $table = 'user_water_bills';

    /**
     * 数据库连接名称
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 搜索用户地址
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchUserAddrAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('user_addr', 'like', '%' . $value . '%');
        }
        return $query;
    }

    /**
     * 搜索用户ID
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchUserIdAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('user_id', $value);
        }
        return $query;
    }

    /**
     * 搜索上级ID
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchParentIdAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('parent_id', $value);
        }
        return $query;
    }

    /**
     * 搜索用户等级
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchUserLevelAttr($query, $value)
    {
        if (!empty($value)) {
            $query->where('user_level', $value);
        }
        return $query;
    }

    /**
     * 搜索流水金额范围
     * @param $query
     * @param $value
     * @return mixed
     */
    public function searchAmountRangeAttr($query, $value)
    {
        if (!empty($value)) {
            $range = explode(',', $value);
            if (count($range) == 2) {
                $query->whereBetween('total_amount', [$range[0], $range[1]]);
            }
        }
        return $query;
    }
}