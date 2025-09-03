<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\DepositLogic;
use plugin\bank\app\validate\DepositValidate;
use support\Request;
use support\Response;

/**
 * 存款控制器
 */
class DepositController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new DepositLogic();
        $this->validate = new DepositValidate();
    }

    /**
     * 列表
     */
    public function index(Request $request): Response
    {
        $data = $this->logic->getList($request->all());
        return $this->success($data);
    }



    /**
     * 获取用户存款记录
     */
    public function getUserDeposits(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $status = $request->input('status', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getUserDeposits($userAddr, $status, $page, $limit);
        return $this->success('ok', $data);
    }

    /**
     * 创建存款订单
     */
    public function createDeposit(Request $request): Response
    {
        $data = $request->all();
        $this->validate->scene('create')->check($data);
        
        $result = $this->logic->createDeposit($data);
        
        if ($result['success']) {
            return $this->success('存款订单创建成功', $result['data']);
        } else {
            return $this->fail($result['message']);
        }
    }

    /**
     * 确认存款
     */
    public function confirmDeposit(Request $request): Response
    {
        $id = $request->input('id');
        $txHash = $request->input('tx_hash');
        
        if (empty($id) || empty($txHash)) {
            return $this->fail('参数错误');
        }
        
        $result = $this->logic->confirmDeposit($id, $txHash);
        
        if ($result['success']) {
            return $this->success('存款确认成功');
        } else {
            return $this->fail($result['message']);
        }
    }

    /**
     * 获取存款统计
     */
    public function getDepositStats(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $data = $this->logic->getDepositStats($userAddr, $startTime, $endTime);
        return $this->success('ok', $data);
    }
}