<?php

namespace app\command;

use plugin\bank\services\UserStatistic;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Statistic extends Command
{
    protected static $defaultName = 'statistic';
    protected static $defaultDescription = 'statistic table data';

    /**
     * 使用常量定义操作名称，避免使用魔法字符串或数字
     */
    private const ACTION_PROCESS = 'process';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('action', InputArgument::OPTIONAL, "要执行的操作: process (或使用数字 1)。留空则不执行任何操作。");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');
        $output->writeln("<info>开始执行 Statistic 命令...</info>");

        // 使用 match 表达式进行路由，代码更清晰、健壮
        match ($action) {
            self::ACTION_PROCESS, '1' => $this->handleProcess($output),
            null, ''                 => $output->writeln('<comment>未指定操作，不执行任何任务。</comment>'),
            default                  => $output->writeln("<error>未知操作: '{$action}'。可用操作: process 或 1。</error>"),
        };

        $output->writeln("<info>命令执行完毕。</info>");
        return self::SUCCESS;
    }

    /**
     * 处理统计数据
     * @param OutputInterface $output
     */
    private function handleProcess(OutputInterface $output): void
    {
        $output->writeln("执行操作: 处理并计算统计表格数据...");

        // 将服务实例化放在需要它的方法内部
        $statisticModel = new \plugin\saiadmin\app\model\more\UserStatistic();
        $userStatisticService = new UserStatistic($statisticModel, 'BWA零线统计表1');

        $userStatisticService->deal_data();
        $userStatisticService->cacl_row();

        $output->writeln('<info>统计表格数据成功。</info>');
    }
}
