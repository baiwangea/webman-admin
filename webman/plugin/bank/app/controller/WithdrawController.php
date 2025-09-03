<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\WithdrawLogic;
use plugin\bank\app\validate\WithdrawValidate;
use support\Request;
use support\Response;

/**
 * 提现控制器
 */
class WithdrawController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new WithdrawLogic();
        $this->validate = new WithdrawValidate();
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
            ['status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }



    /**
     * 获取用户提现记录
     * @param Request $request
     * @return Response
     */
    public function getUserWithdraws(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $status = $request->input('status', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getUserWithdraws($userAddr, $status, $page, $limit);
        return $this->success($data);
    }

    /**
     * 创建提现申请
     * @param Request $request
     * @return Response
     */
    public function createWithdraw(Request $request): Response
    {
        $data = $request->post();
        
        if ($this->validate) {
            if (!$this->validate->scene('create')->check($data)) {
                return $this->fail($this->validate->getError());
            }
        }
        
        $result = $this->logic->createWithdraw($data);
        
        if ($result['code'] == 1) {
            return $this->success($result['msg'], $result['data']);
        } else {
            return $this->fail($result['msg']);
        }
    }

    /**
     * 审核提现申请
     * @param Request $request
     * @return Response
     */
    public function auditWithdraw(Request $request): Response
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $remark = $request->input('remark', '');
        
        if (empty($id) || !in_array($status, [1, 2])) {
            return $this->fail('参数错误');
        }
        
        $result = $this->logic->auditWithdraw($id, $status, $remark);
        
        if ($result['code'] == 1) {
            return $this->success($result['msg']);
        } else {
            return $this->fail($result['msg']);
        }
    }

    /**
     * 确认提现完成
     * @param Request $request
     * @return Response
     */
    public function confirmWithdraw(Request $request): Response
    {
        $id = $request->input('id');
        $txHash = $request->input('tx_hash');
        
        if (empty($id) || empty($txHash)) {
            return $this->fail('参数错误');
        }
        
        $result = $this->logic->confirmWithdraw($id, $txHash);
        
        if ($result['code'] == 1) {
            return $this->success($result['msg']);
        } else {
            return $this->fail($result['msg']);
        }
    }

    /**
     * 获取提现统计
     * @param Request $request
     * @return Response
     */
    public function getWithdrawStats(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $data = $this->logic->getWithdrawStats($userAddr, $startTime, $endTime);
        return $this->success($data);
    }

    /**
     * 更新状态
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
        
        $result = $this->logic->edit(['status' => $status], $id);
        return $result ? $this->success('状态更新成功') : $this->fail('状态更新失败');
    }
}