<?php

use Webman\Route;

// 加载functions文件
require_once __DIR__ . '/../app/functions.php';
use function plugin\bank\app\functions\fastRoute;

// Bank 插件路由配置

// 后台管理路由组
Route::group('/app/bank', function () {
    
    // 用户管理
    fastRoute('users');
    
    // 账户管理
    fastRoute('account');
    
    // 账户记录管理
    fastRoute('accountRecord');
    
    // 公告管理
    fastRoute('announcement');
    
    // 领取管理
    fastRoute('claim');
    
    // 挖矿领取管理
    fastRoute('claimMine');
    
    // 自定义数据管理
    fastRoute('customizeData');
    
    // 存款管理
    fastRoute('deposit');
    
    // 存款等级管理
    fastRoute('depositLevel');
    
    // 存款库存管理
    fastRoute('depositStock');
    
    // 挖矿管理
    fastRoute('mine');
    
    // 订单管理
    fastRoute('orders');
    
    // 平台亏损管理
    fastRoute('platformLoss');
    
    // 用户流水账单管理
    fastRoute('userWaterBills');
    
    // 流水账单记录管理
    fastRoute('waterBillsRecord');
    
});

Route::disableDefaultRoute('bank');