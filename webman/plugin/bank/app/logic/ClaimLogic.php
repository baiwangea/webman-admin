<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\Claim;
use plugin\saiadmin\exception\ApiException;
use plugin\saiadmin\utils\Helper;

/**
 * 领取逻辑层
 */
class ClaimLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new Claim();
    }

    /**
     * 获取用户领取记录
     * @param string $userAddr 用户地址
     * @param array $where 查询条件
     * @return mixed
     */
    public function getUserClaims(string $userAddr, array $where = []): mixed
    {
        $where['user_addr'] = $userAddr;
        $query = $this->search($where);
        $query->order('create_time', 'desc');
        return $this->getList($query);
    }

    /**
     * 创建领取记录
     * @param array $data 领取数据
     * @return mixed
     */
    public function createClaim(array $data): mixed
    {
        return $this->transaction(function () use ($data) {
            // 检查领取资格
            $this->checkClaimEligibility($data['user_addr'], $data['type']);
            
            // 处理数据
            $data = $this->handleCreateData($data);
            
            return $this->add($data);
        });
    }

    /**
     * 处理领取
     * @param int $id 记录ID
     * @param array $data 处理数据
     * @return mixed
     */
    public function processClaim(int $id, array $data): mixed
    {
        return $this->transaction(function () use ($id, $data) {
            $claim = $this->read($id);
            
            if (empty($claim)) {
                throw new ApiException('领取记录不存在');
            }
            
            if ($claim['status'] != 0) {
                throw new ApiException('该领取记录已处理');
            }
            
            // 处理数据
            $updateData = $this->handleProcessData($data);
            
            return $this->edit($id, $updateData);
        });
    }

    /**
     * 获取领取统计
     * @param array $where 查询条件
     * @return array
     */
    public function getClaimStats(array $where = []): array
    {
        $baseQuery = $this->search($where);
        
        return [
            'total_count' => (clone $baseQuery)->count(),
            'pending_count' => (clone $baseQuery)->where('status', 0)->count(),
            'success_count' => (clone $baseQuery)->where('status', 1)->count(),
            'failed_count' => (clone $baseQuery)->where('status', 2)->count(),
            'total_amount' => (clone $baseQuery)->where('status', 1)->sum('amount') ?: 0
        ];
    }

    /**
     * 检查领取资格
     * @param string $userAddr 用户地址
     * @param string $type 领取类型
     * @return bool
     */
    protected function checkClaimEligibility(string $userAddr, string $type): bool
    {
        // 检查今日是否已领取
        $todayStart = strtotime(date('Y-m-d'));
        $todayEnd = $todayStart + 86400;
        
        $todayClaim = $this->model
            ->where('user_addr', $userAddr)
            ->where('type', $type)
            ->where('create_time', '>=', $todayStart)
            ->where('create_time', '<', $todayEnd)
            ->findOrEmpty();
            
        if (!$todayClaim->isEmpty()) {
            throw new ApiException('今日已领取过该类型奖励');
        }
        
        return true;
    }

    /**
     * 处理创建数据
     * @param array $data 原始数据
     * @return array
     */
    protected function handleCreateData(array $data): array
    {
        $data['order_no'] = $this->generateOrderNo();
        $data['status'] = 0; // 待处理
        $data['create_time'] = time();
        
        return $data;
    }

    /**
     * 处理处理数据
     * @param array $data 原始数据
     * @return array
     */
    protected function handleProcessData(array $data): array
    {
        $updateData = [
            'status' => $data['status'],
            'remark' => $data['remark'] ?? '',
            'process_time' => time()
        ];
        
        if ($data['status'] == 1 && !empty($data['tx_hash'])) {
            $updateData['tx_hash'] = $data['tx_hash'];
        }
        
        return $updateData;
    }

    /**
     * 生成订单号
     * @return string
     */
    protected function generateOrderNo(): string
    {
        return 'CL' . date('YmdHis') . Helper::generateRandomString(4, 'number');
    }
}