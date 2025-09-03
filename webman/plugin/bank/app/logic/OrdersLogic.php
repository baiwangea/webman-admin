<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\Orders;

/**
 * 订单逻辑层
 */
class OrdersLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new Orders();
    }

    /**
     * 搜索处理器
     * @param array $where
     * @return \think\db\Query
     */
    public function search(array $where = []): \think\db\Query
    {
        $query = $this->model->withSearch(['user_addr', 'order_no', 'type', 'status'], $where);
        return $query;
    }

    /**
     * 获取用户订单
     * @param string $userAddr
     * @param string $type
     * @param string $status
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getUserOrders(string $userAddr, string $type = '', string $status = '', int $page = 1, int $limit = 20): array
    {
        $query = $this->model->where('user_addr', $userAddr);
        
        if (!empty($type)) {
            $query->where('type', $type);
        }
        
        if (!empty($status)) {
            $query->where('status', $status);
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
     * 创建订单
     * @param array $data
     * @return array
     */
    public function createOrder(array $data): array
    {
        try {
            // 生成订单号
            $data['order_no'] = $this->generateOrderNo($data['type']);
            $data['status'] = 0; // 待处理
            $data['create_time'] = time();
            
            $orderId = $this->add($data);
            
            if ($orderId) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $orderId,
                        'order_no' => $data['order_no']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => '创建订单失败'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * 更新订单状态
     * @param int $id
     * @param int $status
     * @return array
     */
    public function updateOrderStatus(int $id, int $status): array
    {
        try {
            $order = $this->model->find($id);
            
            if (!$order) {
                return ['success' => false, 'message' => '订单不存在'];
            }
            
            $updateData = [
                'status' => $status,
                'update_time' => time()
            ];
            
            // 如果是完成状态，记录完成时间
            if ($status == 2) {
                $updateData['complete_time'] = time();
            }
            
            $result = $this->edit($updateData, $id);
            
            if ($result) {
                return ['success' => true, 'message' => '状态更新成功'];
            } else {
                return ['success' => false, 'message' => '状态更新失败'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * 获取订单统计
     * @param string $userAddr
     * @param int $startTime
     * @param int $endTime
     * @return array
     */
    public function getOrderStats(string $userAddr = '', int $startTime = 0, int $endTime = 0): array
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
        
        // 按状态统计
        $statusStats = $query->field('status, count(*) as count, sum(amount) as total_amount')
            ->group('status')
            ->select()
            ->toArray();
        
        // 按类型统计
        $typeStats = $query->field('type, count(*) as count, sum(amount) as total_amount')
            ->group('type')
            ->select()
            ->toArray();
        
        // 总统计
        $totalStats = [
            'total_orders' => $query->count(),
            'total_amount' => $query->sum('amount') ?: 0,
            'completed_orders' => $query->where('status', 2)->count(),
            'pending_orders' => $query->where('status', 0)->count(),
        ];
        
        return [
            'status_stats' => $statusStats,
            'type_stats' => $typeStats,
            'total_stats' => $totalStats
        ];
    }

    /**
     * 生成订单号
     * @param string $type
     * @return string
     */
    private function generateOrderNo(string $type = ''): string
    {
        $prefix = match($type) {
            'deposit' => 'DEP',
            'withdraw' => 'WTH',
            'transfer' => 'TRF',
            'mine' => 'MIN',
            default => 'ORD'
        };
        
        return $prefix . date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 获取订单详情
     * @param int $id
     * @return array|null
     */
    public function getOrderDetail(int $id): ?array
    {
        $order = $this->model->find($id);
        if (!$order) {
            return null;
        }
        
        return $order->toArray();
    }

    /**
     * 取消订单
     * @param int $id
     * @return array
     */
    public function cancelOrder(int $id): array
    {
        try {
            $order = $this->model->find($id);
            
            if (!$order) {
                return ['success' => false, 'message' => '订单不存在'];
            }
            
            if ($order->status != 0) {
                return ['success' => false, 'message' => '订单状态不允许取消'];
            }
            
            $result = $this->edit([
                'status' => 3, // 已取消
                'update_time' => time()
            ], $id);
            
            if ($result) {
                return ['success' => true, 'message' => '订单取消成功'];
            } else {
                return ['success' => false, 'message' => '订单取消失败'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}