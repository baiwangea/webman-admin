<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 订单表模型
 */
class Orders extends BaseModel
{
    protected $deleteTime = false;
    protected $updateTime = false;
    
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 数据库表名称
     * @var string
     */
    protected $table = 'orders';

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 状态 搜索
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * 金额范围 搜索
     */
    public function searchAmountRangeAttr($query, $value)
    {
        if (is_array($value) && count($value) == 2) {
            $query->whereBetween('amount', $value);
        }
    }
}