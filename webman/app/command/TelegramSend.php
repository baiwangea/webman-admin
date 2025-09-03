<?php

namespace app\command;

use plugin\bank\services\Telegram;
use plugin\bank\services\UserAddr;
use plugin\saiadmin\app\model\more\UserAddr1;
use plugin\saiadmin\app\model\more\UserAddr2;
use plugin\saiadmin\app\model\more\UserAddr3;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramSend extends Command
{
    protected static $defaultName = 'telegram:send';
    protected static $defaultDescription = 'telegram send';

    /**
     * 使用常量定义操作名称，避免使用魔法字符串
     */
    private const ACTION_DEAL_DATA = 'deal_data';
    private const ACTION_CHECK_DATA_LINE = 'check_data_line';
    private const ACTION_CALC_YEJI = 'cacl_yeji';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, '要执行的操作: deal_data | check_data_line | cacl_yeji. 留空则执行默认的导出操作。');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('name');
        $output->writeln("<info>开始执行 Telegram Send 命令...</info>");

        $title = 'BWA-Bank-N5';

        // 将所有需要处理的服务实例放入一个数组，便于迭代，轻松启用或禁用
        $userAddrServices = [
            new UserAddr(new UserAddr1(), $title.'业绩统计表1', $title.'业绩统计表1副本'),
            // new UserAddr(new UserAddr2(), $title.'业绩统计表2', $title.'业绩统计表2副本'),
            // new UserAddr(new UserAddr3(), $title.'业绩统计表3', $title.'业绩统计表3副本'),
        ];

        $telegramService = new Telegram();

        // 使用 match 表达式分发任务
        match ($action) {
            self::ACTION_DEAL_DATA => $this->runOnAllServices($output, $userAddrServices, 'deal_data', '处理大小写、用户ID和推荐关系...'),
            self::ACTION_CHECK_DATA_LINE => $this->runOnAllServices($output, $userAddrServices, 'check_data_line', '检查用户关系，是否在同一条推荐线...'),
            self::ACTION_CALC_YEJI => $this->runOnAllServices($output, $userAddrServices, 'cacl_yeji', '计算业绩...'),
            null, '' => $this->handleDefault($output, $userAddrServices, $telegramService),
            default => $output->writeln("<error>未知操作: '{$action}'</error>"),
        };

        $output->writeln("<info>命令成功执行完毕。</info>");
        return self::SUCCESS;
    }

    /**
     * 处理默认的导出和发送操作
     * @param OutputInterface $output
     * @param array $services
     * @param Telegram $telegramService
     */
    private function handleDefault(OutputInterface $output, array $services, Telegram $telegramService): void
    {
        $output->writeln('执行默认操作：导出业绩并发送到Telegram...');
        foreach ($services as $index => $service) {
            $output->writeln("<comment>--- 正在处理统计表 " . ($index + 1) . " ---</comment>");
            $this->exportAndSend($service, 'export_v1', $telegramService, $output);
            $this->exportAndSend($service, 'export_v2', $telegramService, $output);
        }
    }

    /**
     * 在所有服务实例上运行一个指定的方法
     * @param OutputInterface $output
     * @param array $services
     * @param string $methodName
     * @param string $description
     */
    private function runOnAllServices(OutputInterface $output, array $services, string $methodName, string $description): void
    {
        $output->writeln("执行操作: " . $description);
        foreach ($services as $index => $service) {
            $output->writeln("<comment>--- 正在处理统计表 " . ($index + 1) . " ---</comment>");
            if (method_exists($service, $methodName)) {
                $service->$methodName();
            } else {
                $output->writeln("<error>方法 {$methodName} 在服务实例中不存在。</error>");
            }
        }
    }

    /**
     * 封装了导出、发送和删除文件的通用逻辑
     * @param UserAddr $service
     * @param string $exportMethod
     * @param Telegram $telegramService
     * @param OutputInterface $output
     */
    private function exportAndSend(UserAddr $service, string $exportMethod, Telegram $telegramService, OutputInterface $output): void
    {
        $output->writeln("-> 尝试执行导出方法: {$exportMethod}");
        $fileInfo = $service->$exportMethod();

        if (is_array($fileInfo) && !empty($fileInfo['filepath'])) {
            $filepath = $fileInfo['filepath'];
            $output->writeln("  -> 正在发送文件: {$filepath}");
            $telegramService->sendDocumnet($filepath);
            if (file_exists($filepath)) {
                deleteFile($filepath);
                $output->writeln("  -> 已删除临时文件: {$filepath}");
            }
        } else {
            $output->writeln("  -> <comment>导出失败或未生成文件。</comment>");
        }
    }
}
