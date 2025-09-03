<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\CustomizeData;

/**
 * 自定义数据逻辑层
 */
class CustomizeDataLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new CustomizeData();
    }

    /**
     * 搜索条件处理
     * @param array $searchWhere
     * @return mixed
     */
    public function search(array $searchWhere = []): mixed
    {
        $query = $this->model->newQuery();
        
        if (!empty($searchWhere['key'])) {
            $query->where('key', 'like', '%' . $searchWhere['key'] . '%');
        }
        
        if (!empty($searchWhere['type'])) {
            $query->where('type', $searchWhere['type']);
        }
        
        if (isset($searchWhere['status']) && $searchWhere['status'] !== '') {
            $query->where('status', $searchWhere['status']);
        }
        
        if (!empty($searchWhere['create_time'])) {
            $timeRange = explode(' - ', $searchWhere['create_time']);
            if (count($timeRange) == 2) {
                $query->whereBetweenTime('create_time', $timeRange[0], $timeRange[1]);
            }
        }
        
        return $query->order($this->orderField, $this->orderType);
    }
}