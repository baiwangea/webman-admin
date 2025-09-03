<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\basic;

use support\Request;
use support\Response;
use plugin\saiadmin\basic\BaseController as SaiBaseController;

/**
 * 基类 控制器继承此类
 */
class BaseController extends SaiBaseController
{
    /**
     * 逻辑层注入
     */
    protected $logic;

    /**
     * 验证器注入
     */
    protected $validate;

    /**
     * 初始化
     */
    protected function init(): void
    {
        parent::init();
        // 银行插件特定初始化逻辑
    }

    /**
     * 数据改变后执行
     * @param string $type 类型
     * @param $args
     */
    protected function afterChange(string $type, $args): void
    {
        // 银行插件特定的数据变更后处理
        parent::afterChange($type, $args);
    }
}