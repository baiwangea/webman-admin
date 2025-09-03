<?php

namespace app\command;

use plugin\bank\services\Telegram;
use plugin\bank\services\UserCommunity;
use plugin\saiadmin\app\model\more\UserCommunity1;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ZoneAchieve extends Command
{
    protected static $defaultName = 'zone:achieve';
    protected static $defaultDescription = 'zone achieve';

    /**
     * 使用常量定义操作名称，避免使用魔法字符串，更清晰且不易出错
     */
    private const ACTION_DEAL_DATA = 'deal_data';
    private const ACTION_CALC_YEJI = 'cacl_yeji';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::OPTIONAL, '要执行的操作: deal_data | cacl_yeji. 留空则执行默认的导出操作。');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 使用更具描述性的变量名 $action
        $action = $input->getArgument('name');
        $output->writeln("<info>开始执行 Zone Achieve 命令...</info>");

        // 使用更清晰的变量名
        $communityModel = new UserCommunity1();
        $userCommunityService = new UserCommunity($communityModel, 'BWA小区业绩统计表1');
        $telegramService = new Telegram();

        // 使用 PHP 8.0+ 的 match 表达式，替代 if/elseif 结构，代码更优雅
        match ($action) {
            self::ACTION_DEAL_DATA => $this->handleDealData($output, $userCommunityService),
            self::ACTION_CALC_YEJI => $this->handleCalcYeji($output, $userCommunityService),
            null, ''              => $this->handleDefault($output, $userCommunityService, $telegramService),
            default                => $output->writeln("<error>未知操作: '{$action}'</error>"),
        };

        $output->writeln("<info>命令成功执行完毕。</info>");
        return self::SUCCESS;
    }

    /**
     * 处理默认操作：导出业绩数据并通过Telegram发送。
     * @param OutputInterface $output
     * @param UserCommunity $userCommunityService
     * @param Telegram $telegramService
     */
    private function handleDefault(OutputInterface $output, UserCommunity $userCommunityService, Telegram $telegramService): void
    {
        $output->writeln('执行默认操作：导出小区业绩并发送到Telegram...');
        $fileInfo = $userCommunityService->export();

        if (is_array($fileInfo) && !empty($fileInfo['filepath'])) {
            $filepath = $fileInfo['filepath'];
            $output->writeln("-> 正在发送文件: {$filepath}");
            $telegramService->sendDocumnet($filepath);
            if (file_exists($filepath)) {
                deleteFile($filepath);
                $output->writeln("-> 已删除临时文件: {$filepath}");
            }
        } else {
            $output->writeln('<comment>导出失败或未生成文件。</comment>');
        }
    }

    /**
     * 处理数据处理操作。
     * @param OutputInterface $output
     * @param UserCommunity $userCommunityService
     */
    private function handleDealData(OutputInterface $output, UserCommunity $userCommunityService): void
    {
        $output->writeln('执行操作：处理大小写、用户ID和推荐关系...');
        $userCommunityService->deal_data();
    }

    /**
     * 处理业绩计算操作。
     * @param OutputInterface $output
     * @param UserCommunity $userCommunityService
     */
    private function handleCalcYeji(OutputInterface $output, UserCommunity $userCommunityService): void
    {
        $output->writeln('执行操作：计算小区业绩...');
        $userCommunityService->cacl_yeji();
    }
}
