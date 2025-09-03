<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\OrdersLogic;
use plugin\bank\app\validate\OrdersValidate;
use support\Request;
use support\Response;

/**
 * 订单控制器
 */
class OrdersController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new OrdersLogic();
        $this->validate = new OrdersValidate();
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
            ['order_no', ''],
            ['type', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }



    /**
     * 获取用户订单
     * @param Request $request
     * @return Response
     */
    public function getUserOrders(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $type = $request->input('type', '');
        $status = $request->input('status', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getUserOrders($userAddr, $type, $status, $page, $limit);
        return $this->success($data);
    }

    /**
     * 创建订单
     * @param Request $request
     * @return Response
     */
    public function createOrder(Request $request): Response
    {
        $data = $request->post();
        if (!$this->validate->scene('create')->check($data)) {
            return $this->fail($this->validate->getError());
        }
        
        $result = $this->logic->createOrder($data);
        
        if ($result['success']) {
            return $this->success('订单创建成功', $result['data']);
        } else {
            return $this->fail($result['message']);
        }
    }

    /**
     * 更新订单状态
     * @param Request $request
     * @return Response
     */
    public function updateStatus(Request $request): Response
    {
        $id = $request->input('id');
        $status = $request->input('status');
        
        if (empty($id) || !isset($status)) {
            return $this->fail('参数错误');
        }
        
        $result = $this->logic->updateOrderStatus($id, $status);
        
        if ($result['success']) {
            return $this->success('状态更新成功');
        } else {
            return $this->fail($result['message']);
        }
    }

    /**
     * 获取订单统计
     * @param Request $request
     * @return Response
     */
    public function getOrderStats(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $data = $this->logic->getOrderStats($userAddr, $startTime, $endTime);
        return $this->success($data);
    }
}