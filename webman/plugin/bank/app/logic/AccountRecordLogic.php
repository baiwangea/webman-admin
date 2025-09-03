<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\AccountRecord;

/**
 * 账户记录逻辑层
 */
class AccountRecordLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new AccountRecord();
    }

    /**
     * 搜索处理器
     * @param array $where
     * @return \think\db\Query
     */
    public function search(array $where = []): \think\db\Query
    {
        $query = $this->model->withSearch(['user_addr', 'type', 'status'], $where);
        return $query;
    }

    /**
     * 获取用户账户记录
     * @param string $userAddr
     * @param string $type
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getUserRecords(string $userAddr, string $type = '', int $page = 1, int $limit = 20): array
    {
        $query = $this->model->where('user_addr', $userAddr);
        
        if (!empty($type)) {
            $query->where('type', $type);
        }
        
        $query->order('create_time', 'desc');
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }

    /**
     * 获取账户记录统计
     * @param string $userAddr
     * @param int $startTime
     * @param int $endTime
     * @return array
     */
    public function getRecordStats(string $userAddr = '', int $startTime = 0, int $endTime = 0): array
    {
        $query = $this->model;
        
        if (!empty($userAddr)) {
            $query = $query->where('user_addr', $userAddr);
        }
        
        if ($startTime > 0) {
            $query = $query->where('create_time', '>=', $startTime);
        }
        
        if ($endTime > 0) {
            $query = $query->where('create_time', '<=', $endTime);
        }
        
        // 按类型统计
        $typeStats = $query->field('type, count(*) as count, sum(amount) as total_amount')
            ->group('type')
            ->select()
            ->toArray();
        
        // 总统计
        $totalStats = [
            'total_records' => $query->count(),
            'total_amount' => $query->sum('amount') ?: 0,
        ];
        
        return [
            'type_stats' => $typeStats,
            'total_stats' => $totalStats
        ];
    }

    /**
     * 创建账户记录
     * @param array $data
     * @return int
     */
    public function createRecord(array $data): int
    {
        $data['create_time'] = time();
        return $this->add($data);
    }

    /**
     * 批量创建账户记录
     * @param array $records
     * @return bool
     */
    public function batchCreateRecords(array $records): bool
    {
        $time = time();
        foreach ($records as &$record) {
            $record['create_time'] = $time;
        }
        
        return $this->model->insertAll($records) !== false;
    }

    /**
     * 获取记录详情
     * @param int $id
     * @return array|null
     */
    public function getRecordDetail(int $id): ?array
    {
        $record = $this->model->find($id);
        if (!$record) {
            return null;
        }
        
        return $record->toArray();
    }
}