<?php
declare (strict_types=1);

namespace plugin\bank\services;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use plugin\bank\app\model\AccountRecord;
use plugin\bank\app\model\Claim;
use plugin\bank\app\model\Deposit;
use plugin\bank\app\model\Users;
use plugin\bank\app\model\WaterBillsRecord;
use think\facade\Db;

class UserStatistic
{
    public $model;
    public $exportFileName;
    // 初始化函数
    public function __construct($model, $exportFileName)
    {
        $this->model = $model;
        $this->exportFileName = $exportFileName;
    }

    public function cacl_row()
    {
        try {
            // 生成查询数据
            $where[] = ['my.id', '>', 0];
            $where[] = ['my.status', '=', 1];

            $exportList = $this->model
                ->alias('my')
                ->field('my.*')
                ->where($where)
                ->order("my.id ASC")
                ->select()
                ->toArray();

            $start = strtotime('2024-01-01 00:00:00');
            // $end = strtotime('2025-04-24 23:59:59');
            $end = strtotime(date('Y-m-d 23:59:59'));

            $start1 = strtotime('2025-04-23 00:00:00');
            $end1 = strtotime(date('Y-m-d 23:59:59'));
            $claimModel = new Claim();
            $depositModel = new Deposit();
            $waterBillsRecordModel = new WaterBillsRecord();
            $usersModel = new Users();
            $accountRecordModel = new AccountRecord();
            // 循环处理数据
            foreach ($exportList as $key => $value) {
                # 出金总额
                $self_claim_values = (string)$claimModel->alias('my')
                    ->where([
                        ['my.create_time', 'between', [$start, $end]],
                        ['my.status', '=', 5],
                        ['my.user_addr', '=', $value['user_addr']],
                    ])->sum('claim_values');
                # 入金总额
                $self_deposit_values = (string)$depositModel->alias('my')
                    ->where([
                        ['my.create_time', 'between', [$start, $end]],
                        ['my.deposit_type', '=', 1],
                        ['my.chain_status', '=', 2],
                        ['my.user_addr', '=', $value['user_addr']],
                    ])->sum('deposit_values');

                # 获取伞下USER_ID
                $user = $usersModel->field('user_addr,id,tree')->where('user_addr', '=', $value['user_addr'])->find();
                $tree = ($user['tree'] . $user['id'] . ',');
                $san_ids = $usersModel->where([
                    ['tree', 'like', $tree . '%']
                ])->column('id');
                $team_ids = array_merge($san_ids, [$user['id']]);
                # 团队入金总额
                $team_deposit_values = (string)$depositModel->alias('my')
                    ->where([
                        ['my.create_time', 'between', [$start1, $end1]],
                        ['my.deposit_type', '=', 1],
                        ['my.chain_status', '=', 2],
                        ['my.user_id', 'in', $team_ids],
                    ])->sum('deposit_values');

                # 流水表的值-出金表的值=伞下净值
                $san_water_amount = (string)$waterBillsRecordModel->where([
                    ['taker_id', '=', $user['id']],
                    ['create_time', 'between', [$start, $end]],
                ])->sum('amount');
                $san_claim_values = (string)$claimModel->where([
                    ['status', '=', 5],
                    ['user_id', 'in', $san_ids],
                    ['create_time', 'between', [$start, $end]],
                ])->sum('claim_values');
                $san_net = (float)bcsub($san_water_amount, $san_claim_values, 8);
                # 总奖励金额
                $total_reward = (float)$accountRecordModel->where([
                    ['user_id', '=', $user['id']],
                    ['change_type', 'in', [5, 12, 13, 14, 15, 16, 27, 28, 29, 30, 31]],
                    ['create_time', 'between', [$start, $end]],
                ])->sum('values_change_amount');

                $this->model->where('id', $value['id'])->update([
                    'self_claim_values' => $self_claim_values,
                    'self_deposit_values' => $self_deposit_values,
                    'team_deposit_values' => $team_deposit_values,
                    'san_claim_values' => $san_claim_values,
                    'san_water_amount' => $san_water_amount,
                    'san_net' => $san_net,
                    'total_reward' => $total_reward,
                    'update_at' => date('Y-m-d H:i:s')
                ]);
            }

        } catch (\Exception $e) {
            echo $e->getFile() . PHP_EOL;
            echo $e->getLine() . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;
        }

    }

    public function deal_data()
    {
        // 生成查询数据
        $where[] = ['my.id', '>', 0];
        $where[] = ['my.status', '=', 1];

        $exportList = $this->model
            ->alias('my')
            ->field('my.*')
            ->where($where)
            ->order("my.id DESC")
            ->select()
            ->toArray();
        foreach ($exportList as $key => $value) {
            $user_addr = strtolower(trim($value['user_addr']));
            $user = Db::connect('shop_mysql')
                ->name('users')
                ->alias('my')
                ->where(['my.user_addr' => $user_addr])
                ->find();
            if (empty($user)) {
                $update = [
                    'user_addr' => $user_addr,
                    'update_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $update = [
                    'user_addr' => $user['user_addr'],
                    'parent_id' => $user['parent'],
                    'user_id' => $user['id'],
                    'update_at' => date('Y-m-d H:i:s')
                ];
            }
            if (empty($value['create_at'])) $update['create_at'] = date('Y-m-d H:i:s');
            $this->model->where('id', $value['id'])->update($update);
        }
    }
}   
