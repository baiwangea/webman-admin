<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\PlatformLossLogic;
use plugin\bank\app\validate\PlatformLossValidate;
use support\Request;
use support\Response;

/**
 * 平台亏损控制器
 */
class PlatformLossController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new PlatformLossLogic();
        $this->validate = new PlatformLossValidate();
        parent::__construct();
    }

    /**
     * 数据列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $where = $request->more([
            ['type', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        
        $query = $this->logic->search($where);
        return $this->success('ok', $this->logic->getList($query));
    }
}