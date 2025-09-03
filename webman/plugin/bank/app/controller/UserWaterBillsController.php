<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\UserWaterBillsLogic;
use plugin\bank\app\validate\UserWaterBillsValidate;
use support\Request;
use support\Response;

/**
 * 用户流水账单控制器
 */
class UserWaterBillsController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new UserWaterBillsLogic();
        $this->validate = new UserWaterBillsValidate();
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
            ['user_addr', ''],
            ['type', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        
        $query = $this->logic->search($where);
        return $this->success('ok', $this->logic->getList($query));
    }
}