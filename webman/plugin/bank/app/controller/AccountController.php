<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\AccountLogic;
use plugin\bank\app\validate\AccountValidate;
use support\Request;
use support\Response;

/**
 * 账户管理控制器
 */
class AccountController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new AccountLogic();
        $this->validate = new AccountValidate();
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
            ['user_id', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 获取账户余额
     * @param Request $request
     * @return Response
     */
    public function getBalance(Request $request): Response
    {
        $userAddr = $request->input('user_addr', '');
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $balance = $this->logic->getBalanceByAddr($userAddr);
        return $this->success(['balance' => $balance]);
    }

    /**
     * 修改状态
     * @param Request $request
     * @return Response
     */
    public function changeStatus(Request $request): Response
    {
        $id = $request->input('id', '');
        $status = $request->input('status', 1);
        $model = $this->logic->findOrEmpty($id);
        if ($model->isEmpty()) {
            return $this->fail('未查找到信息');
        }
        $result = $model->save(['status' => $status]);
        if ($result) {
            $this->afterChange('changeStatus', $model);
            return $this->success('操作成功');
        } else {
            return $this->fail('操作失败');
        }
    }


}