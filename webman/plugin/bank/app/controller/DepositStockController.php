<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\DepositStockLogic;
use plugin\bank\app\validate\DepositStockValidate;
use support\Request;
use support\Response;

/**
 * 存款库存控制器
 */
class DepositStockController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new DepositStockLogic();
        $this->validate = new DepositStockValidate();
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
            ['deposit_id', ''],
            ['user_addr', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        
        $query = $this->logic->search($where);
        return $this->success('ok', $this->logic->getList($query));
    }
}