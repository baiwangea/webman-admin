<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\ClaimLogic;
use plugin\bank\app\validate\ClaimValidate;
use support\Request;
use support\Response;

/**
 * 领取控制器
 */
class ClaimController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new ClaimLogic();
        $this->validate = new ClaimValidate();
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
        $data = $this->logic->getList($query);
        return $this->success($data);
    }



    /**
     * 获取用户领取记录
     * @param Request $request
     * @return Response
     */
    public function getUserClaims(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $type = $request->input('type', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getUserClaims($userAddr, $type, $page, $limit);
        return $this->success($data);
    }

    /**
     * 创建领取记录
     * @param Request $request
     * @return Response
     */
    public function createClaim(Request $request): Response
    {
        $data = $request->post();
        
        if ($this->validate) {
            if (!$this->validate->scene('create')->check($data)) {
                return $this->fail($this->validate->getError());
            }
        }
        
        $result = $this->logic->createClaim($data);
        
        if ($result['code'] == 1) {
            return $this->success($result['msg'], $result['data']);
        } else {
            return $this->fail($result['msg']);
        }
    }

    /**
     * 处理领取申请
     * @param Request $request
     * @return Response
     */
    public function processClaim(Request $request): Response
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $remark = $request->input('remark', '');
        
        if (empty($id) || !in_array($status, [1, 2])) {
            return $this->fail('参数错误');
        }
        
        $result = $this->logic->processClaim($id, $status, $remark);
        
        if ($result['code'] == 1) {
            return $this->success($result['msg']);
        } else {
            return $this->fail($result['msg']);
        }
    }

    /**
     * 获取领取统计
     * @param Request $request
     * @return Response
     */
    public function getClaimStats(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $type = $request->input('type');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $data = $this->logic->getClaimStats($userAddr, $type, $startTime, $endTime);
        return $this->success($data);
    }

    /**
     * 检查领取资格
     * @param Request $request
     * @return Response
     */
    public function checkClaimEligibility(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $type = $request->input('type');
        
        if (empty($userAddr) || empty($type)) {
            return $this->fail('参数错误');
        }
        
        $result = $this->logic->checkClaimEligibility($userAddr, $type);
        return $this->success($result);
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