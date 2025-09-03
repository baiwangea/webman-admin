<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        // ----------------------------------------------------------------
        // 示例 Cron 任务 (已注释)
        // ----------------------------------------------------------------

        // 每秒钟执行一次
        // new Crontab('*/1 * * * * *', function () {
        //     echo date('Y-m-d H:i:s') . " - 每秒执行\n";
        // });

        // 每分钟执行一次
        // new Crontab('0 */1 * * * *', function () {
        //     echo date('Y-m-d H:i:s') . " - 每分钟执行\n";
        // });

        // ----------------------------------------------------------------
        // 实际业务 Cron 任务
        // ----------------------------------------------------------------

        // 每10分钟执行一次：处理用户地址数据和推荐线检查
        new Crontab('0 */10 * * * *', function () {
            echo date('Y-m-d H:i:s') . " - 开始执行数据处理任务...\n";
            self::executeCommand('telegram:send deal_data');
            self::executeCommand('telegram:send check_data_line');
            echo date('Y-m-d H:i:s') . " - 数据处理任务执行完毕。\n";
        });

        // 每周一上午10点执行：统计业绩并发送报告 (当前已注释，取消注释即可启用)
        // new Crontab('0 10 * * 1', function () {
        //     echo date('Y-m-d H:i:s') . " - 开始执行每周业绩统计与报告任务...\n";
        //
        //     // 1. 计算N5业绩
        //     self::executeCommand('telegram:send cacl_yeji');
        //
        //     // 2. 导出N5业绩报表并发送
        //     self::executeCommand('telegram:send');
        //
        //     // 3. 计算小区业绩
        //     self::executeCommand('zone:achieve cacl_yeji');
        //
        //     // 4. 导出小区业绩报表并发送
        //     self::executeCommand('zone:achieve');
        //
        //     echo date('Y-m-d H:i:s') . " - 每周业绩统计与报告任务执行完毕。\n";
        // });
    }

    /**
     * 执行一个 webman 命令行任务
     * @param string $command 命令行指令 (例如: "telegram:send deal_data")
     */
    private static function executeCommand(string $command): void
    {
        // 构造完整的命令路径
        $command_path = 'php ' . base_path() . '/webman ' . $command;

        // 使用 passthru 可以直接将命令输出打印到 workerman 的标准输出，方便调试
        passthru($command_path, $return_var);

        // 另一种方式：使用 exec 将输出捕获到日志，适用于生产环境
        // $output = [];
        // exec($command_path, $output, $return_var);
        // if ($return_var !== 0) {
        //     \support\Log::error("Cron command failed: $command_path", $output);
        // } else {
        //     \support\Log::info("Cron command executed: $command_path", $output);
        // }
    }

    // 以下是进程生命周期方法，当前未使用，可保留或移除
    public function initStart() {}
    public function reload() {}
    public function run($args) {}
}