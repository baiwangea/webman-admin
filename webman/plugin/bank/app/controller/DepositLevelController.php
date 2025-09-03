<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\DepositLevelLogic;
use plugin\bank\app\validate\DepositLevelValidate;
use support\Request;
use support\Response;

/**
 * 存款等级控制器
 */
class DepositLevelController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new DepositLevelLogic();
        $this->validate = new DepositLevelValidate();
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
            ['level', ''],
            ['name', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        
        $query = $this->logic->search($where);
        return $this->success('ok', $this->logic->getList($query));
    }
}