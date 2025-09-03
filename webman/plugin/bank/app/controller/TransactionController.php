<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\TransactionLogic;
use plugin\bank\app\validate\TransactionValidate;
use support\Request;
use support\Response;

/**
 * 交易记录控制器
 */
class TransactionController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new TransactionLogic();
        $this->validate = new TransactionValidate();
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
            ['from_addr', ''],
            ['to_addr', ''],
            ['type', ''],
            ['status', ''],
            ['create_time', ''],
            ['amount_min', ''],
            ['amount_max', ''],
        ]);
        
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 获取用户交易记录
     * @param Request $request
     * @return Response
     */
    public function getUserTransactions(Request $request): Response
    {
        $userAddr = $request->input('user_addr', '');
        $type = $request->input('type', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getUserTransactions($userAddr, $type, $page, $limit);
        return $this->success($data);
    }

    /**
     * 获取交易统计
     * @param Request $request
     * @return Response
     */
    public function getTransactionStats(Request $request): Response
    {
        $userAddr = $request->get('user_addr', '');
        $startTime = $request->get('start_time', '');
        $endTime = $request->get('end_time', '');
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getTransactionStats($userAddr, $startTime, $endTime);
        return $this->success('ok', $data);
    }

    /**
     * 创建交易记录
     * @param Request $request
     * @return Response
     */
    public function createTransaction(Request $request): Response
    {
        $data = $request->post();
        
        if (!$this->validate->scene('save')->check($data)) {
            return $this->fail($this->validate->getError());
        }
        
        try {
            $result = $this->logic->createTransaction($data);
            return $this->success('交易记录创建成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 更新交易状态
     * @param Request $request
     * @return Response
     */
    public function updateStatus(Request $request): Response
    {
        $id = $request->post('id');
        $status = $request->post('status');
        $txHash = $request->post('tx_hash', '');
        
        if (empty($id) || !in_array($status, [0, 1, 2])) {
            return $this->fail('参数错误');
        }
        
        try {
            $result = $this->logic->updateTransactionStatus($id, $status, $txHash);
            return $this->success('状态更新成功', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 获取交易详情
     * @param Request $request
     * @param mixed $id
     * @return Response
     */
    public function read(Request $request, $id): Response
    {
        if (empty($id)) {
            return $this->fail('ID不能为空');
        }
        
        $data = $this->logic->getTransactionDetail($id);
        
        if (!$data) {
            return $this->fail('交易记录不存在');
        }
        
        return $this->success('ok', $data);
    }


}