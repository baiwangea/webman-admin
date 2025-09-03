<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\Deposit;
use plugin\bank\app\model\Account;
use plugin\bank\app\logic\AccountLogic;
use plugin\bank\app\logic\AccountRecordLogic;

/**
 * 存款逻辑层
 */
class DepositLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new Deposit();
    }

    /**
     * 搜索处理器
     * @param array $where
     * @return \think\db\Query
     */
    public function search(array $where = []): \think\db\Query
    {
        $query = $this->model->withSearch(['user_addr', 'status', 'level_id'], $where);
        return $query;
    }

    /**
     * 获取用户存款记录
     * @param string $userAddr
     * @param string $status
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getUserDeposits(string $userAddr, string $status = '', int $page = 1, int $limit = 20): array
    {
        $query = $this->model->where('user_addr', $userAddr);
        
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
     * 创建存款订单
     * @param array $data
     * @return array
     */
    public function createDeposit(array $data): array
    {
        try {
            // 验证用户账户是否存在
            $accountLogic = new AccountLogic();
            $account = $accountLogic->getAccountByAddr($data['user_addr']);
            
            if (!$account) {
                return ['success' => false, 'message' => '用户账户不存在'];
            }
            
            // 生成存款订单号
            $data['order_no'] = $this->generateOrderNo();
            $data['status'] = 0; // 待确认
            $data['create_time'] = time();
            
            $depositId = $this->add($data);
            
            if ($depositId) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $depositId,
                        'order_no' => $data['order_no']
                    ]
                ];
            } else {
                return ['success' => false, 'message' => '创建存款订单失败'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * 确认存款
     * @param int $id
     * @param string $txHash
     * @return array
     */
    public function confirmDeposit(int $id, string $txHash): array
    {
        try {
            $deposit = $this->model->find($id);
            
            if (!$deposit) {
                return ['success' => false, 'message' => '存款记录不存在'];
            }
            
            if ($deposit->status != 0) {
                return ['success' => false, 'message' => '存款状态不正确'];
            }
            
            // 更新存款状态
            $this->edit([
                'status' => 1,
                'tx_hash' => $txHash,
                'confirm_time' => time()
            ], $id);
            
            // 更新用户账户余额
            $accountLogic = new AccountLogic();
            $accountLogic->updateBalance($deposit->user_addr, $deposit->amount, 'add');
            
            // 创建账户记录
            $recordLogic = new AccountRecordLogic();
            $recordLogic->createRecord([
                'user_addr' => $deposit->user_addr,
                'type' => 'deposit',
                'amount' => $deposit->amount,
                'tx_hash' => $txHash,
                'remark' => '存款确认',
                'related_id' => $id,
                'related_type' => 'deposit',
                'status' => 1
            ]);
            
            return ['success' => true, 'message' => '存款确认成功'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * 获取存款统计
     * @param string $userAddr
     * @param int $startTime
     * @param int $endTime
     * @return array
     */
    public function getDepositStats(string $userAddr = '', int $startTime = 0, int $endTime = 0): array
    {
        $query = $this->model->where('status', 1); // 只统计已确认的存款
        
        if (!empty($userAddr)) {
            $query = $query->where('user_addr', $userAddr);
        }
        
        if ($startTime > 0) {
            $query = $query->where('confirm_time', '>=', $startTime);
        }
        
        if ($endTime > 0) {
            $query = $query->where('confirm_time', '<=', $endTime);
        }
        
        $totalAmount = $query->sum('amount') ?: 0;
        $totalCount = $query->count();
        
        // 按等级统计
        $levelStats = $query->field('level_id, count(*) as count, sum(amount) as total_amount')
            ->group('level_id')
            ->select()
            ->toArray();
        
        return [
            'total_amount' => $totalAmount,
            'total_count' => $totalCount,
            'level_stats' => $levelStats
        ];
    }

    /**
     * 生成订单号
     * @return string
     */
    private function generateOrderNo(): string
    {
        return 'DEP' . date('YmdHis') . rand(1000, 9999);
    }

    /**
     * 获取存款详情
     * @param int $id
     * @return array|null
     */
    public function getDepositDetail(int $id): ?array
    {
        $deposit = $this->model->find($id);
        if (!$deposit) {
            return null;
        }
        
        return $deposit->toArray();
    }
}