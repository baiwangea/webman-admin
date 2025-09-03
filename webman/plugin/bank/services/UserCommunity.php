<?php
declare (strict_types=1);

namespace plugin\bank\services;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use plugin\bank\app\model\UserWaterBills;
use plugin\bank\app\model\WaterBillsRecord;
use think\facade\Db;

class UserCommunity
{
    public $model;
    public $exportFileName;
    // 初始化函数
    public function __construct($model, $exportFileName)
    {
        $this->model = $model;
        $this->exportFileName = $exportFileName;
    }

    public function export()
    {
        // 生成查询数据
        $where[] = ['my.id', '>', 0];
        $where[] = ['my.status', '=', 1];

        $exportList = $this->model
            ->alias('my')
            ->field('my.*')
            ->where($where)
            ->order("my.serial ASC,my.id DESC")
            ->select()
            ->toArray();

        if (empty($exportList)) {
            throw new \Exception('暂无数据');
        }

        $total_amount = 0;
        $real_amount = 0;
        $day_amount = 0;
        $real_day_amount = 0;
        // 循环处理数据
        foreach ($exportList as $key => $value) {
            if($value['title'] !== '总地址'){
                $total_amount += $value['total_amount'];
                $real_amount += $value['real_amount'];
                $day_amount += $value['day_amount'];
                $real_day_amount += $value['real_day_amount'];
            }
        }
        $exportList[] = [
            'serial' => '',
            'title' => '',
            'user_addr' => '总计：',
            'total_amount' => $total_amount,
            'user_id' => 0,
            'first_parent' => 0,
            'tree' => '',
        ];

        // 获取当前日期
        $endTimestamp = date('Y-m-d H:i:s');
        $periodStr = "截止 {$endTimestamp}";
        $title = $this->exportFileName . " ({$periodStr})";

        // 配置Excel
        $headers = [
            'A' => ['label' => '序号', 'width' => 12, 'field' => 'serial', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'B' => ['label' => '标题', 'width' => 20, 'field' => 'title', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'C' => ['label' => '用户地址', 'width' => 55, 'field' => 'user_addr', 'monospace' => true, 'highlight' => null, 'numberFormat' => null],
            'D' => ['label' => '小区业绩', 'width' => 18, 'field' => 'total_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
        ];
        $export = new ExportExcel();
        return $export->exportExcel($this->exportFileName, $title, $headers, $exportList, 'elegant_classic');
    }

    public function cacl_yeji()
    {
        try {
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
            // 循环处理数据
            foreach ($exportList as $key => $value) {
                $zone_achieve_amount = self::zone_achieve($value['user_id']);
                $this->model->where('id', $value['id'])->update([
                    'total_amount' => $zone_achieve_amount['zone_total_amounts'],
                    'update_at' => date('Y-m-d H:i:s')
                ]);
            }

        }catch (\Exception $e){
            echo $e->getFile().PHP_EOL;
            echo $e->getLine().PHP_EOL;
            echo $e->getMessage().PHP_EOL;
        }

    }

    protected static function zone_achieve($user_id)
    {
        $userInfo = Db::connect('shop_mysql')->name('users')->field('user_addr,id')->where('id',$user_id)->find();
        $waterBillsRecordModel = new WaterBillsRecord();
        $userWaterBillsModel = new UserWaterBills();
        # 查出来B,C,D的伞下流水情况
        $waterAmounts = $userWaterBillsModel->field('user_id,total_amount,user_addr')->where('parent_id','=',$user_id)->select()->toArray();
        # $waterAmounts = array_column($waterAmounts, 'total_amount', 'user_id');

        $zone_total_amounts = 0;
        $max_zone_total_amount = 0;
        # 然后再分别查询B,C,D的给A产生的流水
        foreach ($waterAmounts as $key => $value) {
            $waterAmounts[$key]['self_water_amount'] = $self_water_amount = $waterBillsRecordModel->where([
                'payer_id' => $value['user_id'],
                'taker_id' => $user_id,
            ])->sum('amount');
            $waterAmounts[$key]['zone_total_amount'] = $zone_total_amount = $self_water_amount + $value['total_amount'];
            $zone_total_amounts += $zone_total_amount;
            if($zone_total_amount >= $max_zone_total_amount) $max_zone_total_amount = $zone_total_amount;
            $waterAmounts[$key]['total_amount'] = floatval($value['total_amount']);
        }
        # 减掉最大的那一个(如果存在多个相同最大值，则随便减一个),剩余的总数量就是A用户的小区流水金额
        $zone_total_amounts -= $max_zone_total_amount;
        return [
            'user_info' => $userInfo,
            'list' => $waterAmounts,
            'zone_total_amounts' => $zone_total_amounts,
        ];
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
            ->order("my.serial ASC,my.id DESC")
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
                    // 'tree' => $user['tree'],
                    'update_at' => date('Y-m-d H:i:s')
                ];
            }
            if(empty($value['create_at'])) $update['create_at'] = date('Y-m-d H:i:s');
            $this->model->where('id', $value['id'])->update($update);
        }
    }

    /**
     * 判断是否横线，即不在同一条推荐树上
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check_data_line()
    {
        // 生成查询数据
        $where[] = ['my.id', '>', 0];
        $where[] = ['my.status', '=', 1];

        $list = $this->model
            ->alias('my')
            ->field('my.*')
            ->where($where)
            ->order("my.serial ASC,my.id DESC")
            ->select()
            ->toArray();
        $user_ids = array_column($list, 'user_id');
        foreach ($list as $value) {
            $remark = '正常';
            $first_parent = 0;
            $com_tree = '';
            $tree = array_filter(explode(',', $value['tree']));
            $common = array_values(array_intersect($tree, $user_ids));
            if (!empty($common)) {
                $first_parent = $common[count($common) - 1];
                $com_tree = implode(',', $common);
                $remark = '同一条线包含：' . $com_tree;
            }
            $this->model->where('id', $value['id'])->update([
                'first_parent' => $first_parent,
                'com_tree' => $com_tree,
                'remark' => $remark
            ]);
        }
    }
}   
