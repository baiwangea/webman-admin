<?php
declare (strict_types=1);

namespace plugin\bank\services;
use think\facade\Db;

class UserAddrLogs
{
    // 初始化函数
    public function __construct()
    {
        $this->model = new \app\common\model\system\UserAddrLogs();
    }

    public function addAddrLogs($data)
    {
        $this->model->insert($data);
    }

    /**
     * 灵活计算奖励次数（前N次自定义门槛，后续按固定增量）
     * @param float $achievement 实际业绩额（单位：元）
     * @param array $customThresholds 前N次的自定义门槛 [第1次 => 15000, 第2次 => 45000, ...]
     * @param int $increment 后续每次奖励的业绩增量（单位：元，默认3万）
     * @param int $maxTimes 最大奖励次数（默认10次）
     * @return int 奖励次数
     */
    function get_award_num(
        $achievement,
        array $customThresholds = [
            1 => 15000,
        ],
        $increment = 30000,
        $maxTimes = 10
    ) {
        $times = 0;

        // 1. 检查前N次的自定义门槛
        foreach ($customThresholds as $time => $threshold) {
            if ($achievement >= $threshold) {
                $times = $time;
            } else {
                break; // 未达到当前次数门槛，终止检查
            }
        }

        // 2. 处理后续的固定增量规则
        if (!empty($customThresholds)) {
            $lastCustomTime = max(array_keys($customThresholds));
            $lastCustomThreshold = max($customThresholds);
            if ($times >= $lastCustomTime) {
                $remaining = $achievement - $lastCustomThreshold;
                $times += (int)($remaining / $increment);
            }
        }

        // 3. 不超过最大次数
        return min($times, $maxTimes);
    }

    /**
     * 获取累积奖励次数
     * @param $total_amount
     * @param $today_amount
     * @return int|null
     */
    public function get_total_award($total_amount, $today_amount, $real_award = 0)
    {
        $yesterday_amount = $total_amount - $today_amount;
        $total_award = $this->get_award_num($yesterday_amount);
        return max($total_award, $real_award);
    }

    /**
     * 获取本期奖励次数
     * @param $total_amount
     * @param $today_amount
     * @return int|null
     */
    public function get_today_award($total_amount, $today_amount, $real_award = 0)
    {
        $yesterday_amount = $total_amount - $today_amount;
        $yesterday = $this->get_award_num($yesterday_amount);
        $today = $this->get_award_num($total_amount);
        if((float)$today > $real_award && (float)$today > (float)$yesterday){
            // return $today; # 本次该奖励到第几次
            return ($today-$yesterday); # 本期应该给多少次奖励
        }
        return 0;
    }
}   
