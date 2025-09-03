<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\saiadmin\basic\BaseLogic;
use plugin\bank\app\model\Mine;

/**
 * 挖矿逻辑层
 */
class MineLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new Mine();
    }

    /**
     * 搜索器
     * @param array $searchWhere
     * @return mixed
     */
    public function search(array $searchWhere = []): mixed
    {
        $query = $this->model->newQuery();
        
        // 按名称搜索
        if (!empty($searchWhere['name'])) {
            $query->where('name', 'like', '%' . $searchWhere['name'] . '%');
        }
        
        // 按类型搜索
        if (!empty($searchWhere['type'])) {
            $query->where('type', $searchWhere['type']);
        }
        
        // 按状态搜索
        if (isset($searchWhere['status']) && $searchWhere['status'] !== '') {
            $query->where('status', $searchWhere['status']);
        }
        
        // 按创建时间搜索
        if (!empty($searchWhere['create_time'])) {
            if (is_array($searchWhere['create_time']) && count($searchWhere['create_time']) == 2) {
                $query->whereBetween('create_time', $searchWhere['create_time']);
            }
        }
        
        return $query->order('id', 'desc');
    }

    /**
     * 获取挖矿列表
     * @param string $type
     * @param int $status
     * @return array
     */
    public function getMineList($type = '', $status = 1)
    {
        $query = $this->model->newQuery();
        
        if (!empty($type)) {
            $query->where('type', $type);
        }
        
        if ($status !== '') {
            $query->where('status', $status);
        }
        
        $list = $query->order('sort', 'desc')
                     ->order('id', 'desc')
                     ->select()
                     ->toArray();
        
        return $list;
    }

    /**
     * 获取挖矿详情
     * @param int $id
     * @return array|null
     */
    public function getMineDetail($id)
    {
        $mine = $this->model->find($id);
        
        if (!$mine) {
            return null;
        }
        
        $data = $mine->toArray();
        
        // 计算收益率等信息
        if ($data['type'] == 'fixed') {
            $data['daily_rate'] = $data['rate'] / 100;
            $data['total_rate'] = ($data['rate'] / 100) * $data['duration'];
        } else {
            $data['daily_rate'] = $data['rate'] / 100;
            $data['total_rate'] = 0; // 浮动收益无法预计总收益
        }
        
        return $data;
    }

    /**
     * 创建挖矿项目
     * @param array $data
     * @return int
     */
    public function createMine($data)
    {
        // 设置默认值
        $data['create_time'] = time();
        $data['update_time'] = time();
        
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        
        if (!isset($data['sort'])) {
            $data['sort'] = 0;
        }
        
        return $this->add($data);
    }

    /**
     * 更新挖矿项目
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateMine($data, $id)
    {
        $data['update_time'] = time();
        return $this->edit($data, $id);
    }

    /**
     * 获取挖矿统计
     * @param string $type
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    public function getMineStats($type = '', $startTime = '', $endTime = '')
    {
        $query = $this->model->newQuery();
        
        if (!empty($type)) {
            $query->where('type', $type);
        }
        
        if (!empty($startTime) && !empty($endTime)) {
            $query->whereBetween('create_time', [strtotime($startTime), strtotime($endTime)]);
        }
        
        $stats = [
            'total_count' => $query->count(),
            'active_count' => (clone $query)->where('status', 1)->count(),
            'inactive_count' => (clone $query)->where('status', 0)->count(),
            'avg_rate' => (clone $query)->avg('rate') ?: 0,
            'max_rate' => (clone $query)->max('rate') ?: 0,
            'min_rate' => (clone $query)->min('rate') ?: 0,
        ];
        
        // 按类型统计
        $typeStats = $this->model->newQuery()
                                ->field('type, count(*) as count, avg(rate) as avg_rate')
                                ->group('type')
                                ->select()
                                ->toArray();
        
        $stats['type_stats'] = $typeStats;
        
        return $stats;
    }

    /**
     * 获取热门挖矿项目
     * @param int $limit
     * @return array
     */
    public function getHotMines($limit = 10)
    {
        $list = $this->model->where('status', 1)
                           ->order('rate', 'desc')
                           ->order('sort', 'desc')
                           ->limit($limit)
                           ->select()
                           ->toArray();
        
        return $list;
    }

    /**
     * 检查挖矿项目是否可用
     * @param int $id
     * @return bool
     */
    public function checkMineAvailable($id)
    {
        $mine = $this->model->find($id);
        
        if (!$mine || $mine->status != 1) {
            return false;
        }
        
        // 检查是否在有效期内
        $currentTime = time();
        if ($mine->start_time && $currentTime < $mine->start_time) {
            return false;
        }
        
        if ($mine->end_time && $currentTime > $mine->end_time) {
            return false;
        }
        
        return true;
    }
}