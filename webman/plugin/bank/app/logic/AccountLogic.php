<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\Account;
use plugin\saiadmin\exception\ApiException;

/**
 * 账户逻辑层
 */
class AccountLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new Account();
    }

    /**
     * 根据地址获取账户余额
     * @param string $userAddr
     * @return string
     */
    public function getBalanceByAddr(string $userAddr): string
    {
        $account = $this->model->where('user_addr', $userAddr)->find();
        if (!$account) {
            return '0.00000000';
        }
        return $this->formatAmount($account->balance);
    }

    /**
     * 读取数据
     * @param $id
     * @return array
     */
    public function read($id): array
    {
        $model = $this->model->findOrEmpty($id);
        if ($model->isEmpty()) {
            throw new ApiException('数据不存在');
        }
        return $model->toArray();
    }

    /**
     * 添加数据
     * @param array $data
     * @return mixed
     */
    public function save(array $data): mixed
    {
        return $this->handleData('save', $data);
    }

    /**
     * 处理数据
     * @param string $scene
     * @param array $data
     * @return mixed
     */
    protected function handleData(string $scene, array $data): mixed
    {
        if ($scene === 'save') {
            // 验证地址格式
            if (!$this->validateAddress($data['user_addr'])) {
                throw new ApiException('用户地址格式不正确');
            }
            
            // 检查账户是否已存在
            $exists = $this->model->where('user_addr', $data['user_addr'])->find();
            if ($exists) {
                throw new ApiException('该地址账户已存在');
            }
            
            $data['balance'] = $data['balance'] ?? '0.00000000';
            $data['status'] = $data['status'] ?? 1;
        }
        
        return $this->model->save($data) ? $this->model->getKey() : false;
    }

    /**
     * 更新账户余额
     * @param string $userAddr
     * @param string $amount
     * @param string $type add|sub
     * @return bool
     */
    public function updateBalance(string $userAddr, string $amount, string $type = 'add'): bool
    {
        $account = $this->model->where('user_addr', $userAddr)->find();
        if (!$account) {
            throw new ApiException('账户不存在');
        }
        
        $currentBalance = (float)$account->balance;
        $changeAmount = (float)$amount;
        
        if ($type === 'add') {
            $newBalance = $currentBalance + $changeAmount;
        } else {
            if ($currentBalance < $changeAmount) {
                throw new ApiException('账户余额不足');
            }
            $newBalance = $currentBalance - $changeAmount;
        }
        
        return $account->save([
            'balance' => $this->formatAmount($newBalance),
            'update_time' => time()
        ]);
    }

    /**
     * 获取用户账户列表
     * @param int $userId
     * @return array
     */
    public function getUserAccounts(int $userId): array
    {
        return $this->model->where('user_id', $userId)
            ->order('create_time', 'desc')
            ->select()
            ->toArray();
    }
}