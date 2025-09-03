<?php
declare (strict_types=1);

namespace plugin\bank\services;

use think\facade\Db;

class UserAddr
{
    public $model;
    public $exportFileName;
    public $exportFileNameV2;
    // 初始化函数
    public function __construct($model, $exportFileName, $exportFileNameV2)
    {
        $this->model = $model;
        $this->exportFileName = $exportFileName;
        $this->exportFileNameV2 = $exportFileNameV2;
    }
    public function export_v1()
    {
        // 生成查询数据
        $where[] = ['my.id', '>', 0];
        $where[] = ['my.status', '=', 1];

        $exportList = $this->model
            ->alias('my')
            ->field('my.*')
            ->with('yeji')
            ->where($where)
            ->order("my.serial ASC,my.id DESC")
            ->select()
            ->toArray();

        if (empty($exportList)) {
            throw new \Exception('暂无数据');
        }

        // 计算总计
        $totals = [
            'total_amount' => 0,
            'real_amount' => 0,
            'day_amount' => 0,
            'real_day_amount' => 0,
        ];
        foreach ($exportList as $k => $value) {
            # 正常流程算法
            if($value['real_amount'] > $value['real_amount_0']){
                $exportList[$k]['real_amount'] = $value['real_amount_0'];
                $exportList[$k]['real_day_amount'] = $value['real_day_amount_0'];
            }else{
                $exportList[$k]['real_amount'] = $value['real_amount'];
                $exportList[$k]['real_day_amount'] = $value['real_day_amount'];
            }
            if ($value['title'] !== '总地址') {
                $totals['total_amount'] += (float)$value['total_amount'];
                $totals['real_amount'] += (float)$exportList[$k]['real_amount'];
                $totals['day_amount'] += (float)$value['day_amount'];
                $totals['real_day_amount'] += (float)$exportList[$k]['real_day_amount'];
            }
        }

        // 获取统计日期
        $lastDate = $this->get_statistics_dates();
        $startTimestamp = date('Y-m-d H:i:s', strtotime($lastDate[1]));
        $endTimestamp = date('Y-m-d H:i:s', strtotime($lastDate[0]) - 1);
        $periodStr = "本期 {$startTimestamp} ~ {$endTimestamp}";
        $title = $this->exportFileName . " ({$periodStr})";

        // 添加总计行
        $exportList[] = [
            'serial' => '',
            'title' => '',
            'user_addr' => '总计：',
            'total_amount' => $totals['total_amount'],
            'real_amount' => $totals['real_amount'],
            'day_amount' => $totals['day_amount'],
            'real_day_amount' => $totals['real_day_amount'],
            'total_award' => 0,
            'today_award' => 0,
            'user_id' => 0,
            'first_parent' => 0,
            'tree' => '',
            'create_at' => '',
        ];

        // 配置Excel
        $headers = [
            'A' => ['label' => '序号', 'width' => 12, 'field' => 'serial', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'B' => ['label' => '标题', 'width' => 20, 'field' => 'title', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'C' => ['label' => '用户地址', 'width' => 55, 'field' => 'user_addr', 'monospace' => true, 'highlight' => null, 'numberFormat' => null],
            'D' => ['label' => '伞下业绩', 'width' => 18, 'field' => 'total_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
            'E' => ['label' => '本期新增', 'width' => 18, 'field' => 'day_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
            'F' => ['label' => '累积奖励次数', 'width' => 18, 'field' => 'total_award', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'G' => ['label' => '本期奖励次数', 'width' => 18, 'field' => 'today_award', 'monospace' => false, 'highlight' => function($row) { return (float)$row['today_award'] > 0; }, 'numberFormat' => null],
            'H' => ['label' => '用户ID', 'width' => 15, 'field' => 'user_id', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'I' => ['label' => '最近上级ID', 'width' => 15, 'field' => 'first_parent', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'J' => ['label' => '添加时间', 'width' => 20, 'field' => 'create_at', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
        ];
        $export = new ExportExcel();
        return $export->exportExcel($this->exportFileName, $title, $headers, $exportList);
    }
    public function export_v2()
    {
        // 生成查询数据
        $where[] = ['my.id', '>', 0];
        $where[] = ['my.status', '=', 1];

        $exportList = $this->model
            ->alias('my')
            ->field('my.*')
            ->with('yeji')
            ->where($where)
            ->order("my.serial ASC,my.id DESC")
            ->select()
            ->toArray();

        if (empty($exportList)) {
            throw new \Exception('暂无数据');
        }

        // 计算总计
        $totals = [
            'total_amount' => 0,
            'real_amount' => 0,
            'day_amount' => 0,
            'real_day_amount' => 0,
        ];
        foreach ($exportList as $k => $value) {
            # 正常流程算法
            if($value['real_amount'] > $value['real_amount_0']){
                $exportList[$k]['real_amount'] = $value['real_amount_0'];
                $exportList[$k]['real_day_amount'] = $value['real_day_amount_0'];
            }else{
                $exportList[$k]['real_amount'] = $value['real_amount'];
                $exportList[$k]['real_day_amount'] = $value['real_day_amount'];
            }
            if ($value['title'] !== '总地址') {
                $totals['total_amount'] += (float)$value['total_amount'];
                $totals['real_amount'] += (float)$exportList[$k]['real_amount'];
                $totals['day_amount'] += (float)$value['day_amount'];
                $totals['real_day_amount'] += (float)$exportList[$k]['real_day_amount'];
            }
        }

        // 获取统计日期
        $lastDate = $this->get_statistics_dates();
        $startTimestamp = date('Y-m-d H:i:s', strtotime($lastDate[1]));
        $endTimestamp = date('Y-m-d H:i:s', strtotime($lastDate[0]) - 1);
        $periodStr = "本期 {$startTimestamp} ~ {$endTimestamp}";
        $title = $this->exportFileName . " ({$periodStr})";

        // 添加总计行
        $exportList[] = [
            'serial' => '',
            'title' => '',
            'user_addr' => '总计：',
            'total_amount' => $totals['total_amount'],
            'real_amount' => $totals['real_amount'],
            'day_amount' => $totals['day_amount'],
            'real_day_amount' => $totals['real_day_amount'],
            'total_award' => 0,
            'today_award' => 0,
            'user_id' => 0,
            'first_parent' => 0,
            'tree' => '',
            'create_at' => '',
        ];

        // 配置Excel
        $headers = [
            'A' => ['label' => '序号', 'width' => 12, 'field' => 'serial', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'B' => ['label' => '标题', 'width' => 20, 'field' => 'title', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'C' => ['label' => '用户地址', 'width' => 55, 'field' => 'user_addr', 'monospace' => true, 'highlight' => null, 'numberFormat' => null],
            'D' => ['label' => '伞下业绩', 'width' => 18, 'field' => 'total_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
            'E' => ['label' => '实际伞下业绩', 'width' => 18, 'field' => 'real_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
            'F' => ['label' => '本期新增', 'width' => 18, 'field' => 'day_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
            'G' => ['label' => '实际本期新增', 'width' => 18, 'field' => 'real_day_amount', 'monospace' => false, 'highlight' => null, 'numberFormat' => '#,##0.00'],
            'H' => ['label' => '累积奖励次数', 'width' => 18, 'field' => 'total_award', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'I' => ['label' => '本期奖励次数', 'width' => 18, 'field' => 'today_award', 'monospace' => false, 'highlight' => function($row) { return (float)$row['today_award'] > 0; }, 'numberFormat' => null],
            'J' => ['label' => '用户ID', 'width' => 15, 'field' => 'user_id', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'K' => ['label' => '最近上级ID', 'width' => 15, 'field' => 'first_parent', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
            'L' => ['label' => '添加时间', 'width' => 20, 'field' => 'create_at', 'monospace' => false, 'highlight' => null, 'numberFormat' => null],
        ];
        $export = new ExportExcel();
        return $export->exportExcel($this->exportFileNameV2, $title, $headers, $exportList);
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
                ->with('yeji')
                ->where($where)
                ->order("my.first_parent ASC,my.id DESC")
                ->select()
                ->toArray();

            $initDateBool = false;
            $currentDate = date('Y-m-d');
            $initDate = '2025-05-26';
            if ($currentDate === $initDate) {
                $initDateBool = true;
            }
            # 总业绩从2025-05-10零点开始计算
            $startTimestamp0 = strtotime('2025-05-10 00:00:00');
            $lastDate = $this->get_statistics_dates();
            $startTimestamp = strtotime($lastDate[1]);
            $endTimestamp = strtotime($lastDate[0])-1;
            // 循环处理数据
            foreach ($exportList as $key => $value) {
                // $yeji_amount = $exportList[$key]['yeji_amount'] = isset($value['yeji']['total_amount']) ? $value['yeji']['total_amount'] : 0;
                # 2025-05-16 总业绩从添加的时刻开始统计
                $create_at = strtotime($value['create_at']);
                $create_at_0 = strtotime(date('Y-m-d', $create_at));
                if($create_at < strtotime('2025-05-27 10:00:00')){
                    $yeji_amount = $exportList[$key]['yeji_amount'] = Db::connect('shop_mysql')
                        ->name('water_bills_record')
                        ->alias('sm')
                        ->where([
                            ['sm.taker_id', '=', $value['user_id']],
                            ['sm.create_time', 'between', [$startTimestamp0, $endTimestamp]],
                        ])
                        ->sum('real_amount');;
                }else{
                    $yeji_amount = $exportList[$key]['yeji_amount'] = Db::connect('shop_mysql')
                        ->name('water_bills_record')
                        ->alias('sm')
                        ->where([
                            ['sm.taker_id', '=', $value['user_id']],
                            ['sm.create_time', 'between', [$create_at_0, $endTimestamp]],
                        ])
                        ->sum('real_amount');
                }
                if($create_at >= $startTimestamp){
                    $startTimestampReal = $create_at_0;
                }else{
                    $startTimestampReal = $startTimestamp;
                }

                // 每日新增 select sum(real_amount) from water_bills_record where taker_id = ? and create_time > ? and create_time < ?;
                $day_amount = Db::connect('shop_mysql')
                    ->name('water_bills_record')
                    ->alias('sm')
                    ->where([
                        ['sm.taker_id', '=', $value['user_id']],
                        ['sm.create_time', 'between', [$startTimestampReal, $endTimestamp]],
                    ])
                    ->sum('real_amount');
                // 减去重复业绩
                if ($value['first_parent'] > 0) {
                    $parent_real_amount = $this->model->where('user_id', $value['first_parent'])->value('real_amount');
                    $parent_real_day_amount = $this->model->where('user_id', $value['first_parent'])->value('real_day_amount');
                    if($parent_real_amount > $yeji_amount){
                        $this->model->where('user_id', $value['first_parent'])->update(['real_amount' => DB::raw('real_amount - ' . $yeji_amount)]);
                    }else{
                        $this->model->where('user_id', $value['first_parent'])->update(['real_amount' => 0]);
                    }
                    if($parent_real_day_amount > $day_amount){
                        $this->model->where('user_id', $value['first_parent'])->update(['real_day_amount' => DB::raw('real_day_amount - ' . $day_amount)]);
                    }else{
                        $this->model->where('user_id', $value['first_parent'])->update(['real_day_amount' => 0]);
                    }
                }

                # 正常流程算法
                $yeji_amount_0 = $exportList[$key]['yeji_amount'] = Db::connect('shop_mysql')
                    ->name('water_bills_record')
                    ->alias('sm')
                    ->where([
                        ['sm.taker_id', '=', $value['user_id']],
                        ['sm.create_time', 'between', [$startTimestamp0, $endTimestamp]],
                    ])
                    ->sum('real_amount');
                $day_amount_0 = Db::connect('shop_mysql')
                    ->name('water_bills_record')
                    ->alias('sm')
                    ->where([
                        ['sm.taker_id', '=', $value['user_id']],
                        ['sm.create_time', 'between', [$startTimestamp, $endTimestamp]],
                    ])
                    ->sum('real_amount');
                if ($value['first_parent'] > 0) {
                    $parent_real_amount_0 = $this->model->where('user_id', $value['first_parent'])->value('real_amount_0');
                    $parent_real_day_amount_0 = $this->model->where('user_id', $value['first_parent'])->value('real_day_amount_0');
                    if($parent_real_amount_0 > $yeji_amount_0){
                        $this->model->where('user_id', $value['first_parent'])->update(['real_amount_0' => DB::raw('real_amount_0 - ' . $yeji_amount_0)]);
                    }else{
                        $this->model->where('user_id', $value['first_parent'])->update(['real_amount_0' => 0]);
                    }
                    if($parent_real_day_amount_0 > $day_amount_0){
                        $this->model->where('user_id', $value['first_parent'])->update(['real_day_amount_0' => DB::raw('real_day_amount_0 - ' . $day_amount_0)]);
                    }else{
                        $this->model->where('user_id', $value['first_parent'])->update(['real_day_amount_0' => 0]);
                    }
                }
                $this->model->where('id', $value['id'])->update([
                    'total_amount' => $yeji_amount,
                    'real_amount' => $yeji_amount,
                    'day_amount' => $day_amount,
                    'real_day_amount' => $day_amount,

                    'total_amount_0' => $yeji_amount_0,
                    'real_amount_0' => $yeji_amount_0,
                    'day_amount_0' => $day_amount_0,
                    'real_day_amount_0' => $day_amount_0,
                    'update_at' => date('Y-m-d H:i:s')
                ]);
            }

            # 入库日志
            $userAddrLogsServ = new \app\services\UserAddrLogs();
            $exportNewList = $this->model
                ->alias('my')
                ->field('my.*')
                ->where($where)
                ->order("my.serial ASC")
                ->select()
                ->toArray();
            foreach ($exportNewList as $key => $value) {
                if($value['title'] == '总地址') continue;
                $create_at = $value['create_at'];
                # 实际奖励次数
                if($value['real_award_at'] < $startTimestamp){
                    $has_award = $value['total_award'] + $value['today_award'];
                }else{
                    $has_award = $value['real_award'];
                }

                $real_award = max($value['real_award'], $has_award);
                # 正常流程算法
                if($value['real_amount'] > $value['real_amount_0']){
                    $real_amount = $value['real_amount_0'];
                    $real_day_amount = $value['real_day_amount_0'];
                }else{
                    $real_amount = $value['real_amount'];
                    $real_day_amount = $value['real_day_amount'];
                }
                # 初始化奖励次数按照总业绩计算
                if($initDateBool === true){
                    $updateData = [
                        'total_award' => $userAddrLogsServ->get_total_award((float)$real_amount, (float)$real_amount, $real_award) ?? 0,
                        'today_award' => $userAddrLogsServ->get_award_num((float)$real_amount) ?? 0,
                        'update_at' => date('Y-m-d H:i:s')
                    ];
                }else{
                    # 判断是否为本期新增的，累积奖励次数为0，本期奖励次数按照总业绩计算
                    if($create_at >= $startTimestamp){
                        # 按照从开始到本期结束的业绩，来计算奖励次数
                        $total_award = $userAddrLogsServ->get_total_award((float)$real_amount, (float)$real_amount, $real_award) ?? 0;
                        $today_award = $userAddrLogsServ->get_award_num((float)$real_amount) ?? 0;

                        # 按照本期的业绩，来计算本次奖励次数
                        // $total_award = $userAddrLogsServ->get_total_award((float)$real_amount, (float)$real_day_amount, $real_award) ?? 0;
                        // $today_award = $userAddrLogsServ->get_today_award((float)$real_amount, (float)$real_day_amount, $real_award) ?? 0;
                    }else{
                        $total_award = $userAddrLogsServ->get_total_award((float)$real_amount, (float)$real_day_amount, $real_award) ?? 0;
                        $today_award = $userAddrLogsServ->get_today_award((float)$real_amount, (float)$real_day_amount, $real_award) ?? 0;
                    }
                    $updateData = [
                        'real_award' => $real_award,
                        'total_award' => $total_award,
                        'today_award' => $today_award,
                        'update_at' => date('Y-m-d H:i:s'),
                        'real_award_at' => date('Y-m-d H:i:s'),
                    ];
                }
                $this->model->where('id', $value['id'])->update($updateData);
            }

        }catch (\Exception $e){
            echo $e->getFile().PHP_EOL;
            echo $e->getLine().PHP_EOL;
            echo $e->getMessage().PHP_EOL;
        }

    }

    /**
     * 获取统计日期段
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function get_statistics_dates()
    {
        $lastDate = getRecentMondayDates();
        $twoDate = Db::name('statistics_dates')->limit(2)->where('calc_at', '<=', date('Y-m-d H:i:s'))->order('calc_at DESC')->column('calc_at');
        $fourDate = array_unique(array_merge($lastDate, $twoDate));
        rsort($fourDate);
        foreach ($fourDate as $value) {
            if (Db::name('statistics_dates')->where(['calc_at' => $value])->count() == 0) {
                Db::name('statistics_dates')->insert([
                    'calc_at' => $value,
                    'status' => 1,
                ]);
            }
        }
        return array_slice($fourDate, 0, 2);
    }

    public function deal_data()
    {
        // 生成查询数据
        $where[] = ['my.id', '>', 0];
        $where[] = ['my.status', '=', 1];

        $exportList = $this->model
            ->alias('my')
            ->field('my.*')
            ->with('yeji')
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
                    'tree' => $user['tree'],
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
            ->with('yeji')
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
