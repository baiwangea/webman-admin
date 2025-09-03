<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\ClaimMineLogic;
use plugin\bank\app\validate\ClaimMineValidate;
use support\Request;
use support\Response;

/**
 * 挖矿领取控制器
 */
class ClaimMineController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new ClaimMineLogic();
        $this->validate = new ClaimMineValidate();
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
            ['mine_id', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        
        $query = $this->logic->search($where);
        return $this->success('ok', $this->logic->getList($query));
    }
}