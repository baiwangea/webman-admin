<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\MineLogic;
use plugin\bank\app\validate\MineValidate;
use support\Request;
use support\Response;

/**
 * 挖矿控制器
 */
class MineController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new MineLogic();
        $this->validate = new MineValidate();
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
            ['name', ''],
            ['type', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }



    /**
     * 获取挖矿列表
     * @param Request $request
     * @return Response
     */
    public function getMineList(Request $request): Response
    {
        $type = $request->input('type', '');
        $status = $request->input('status', 1);
        
        $data = $this->logic->getMineList($type, $status);
        return $this->success($data);
    }

    /**
     * 获取挖矿详情
     * @param Request $request
     * @return Response
     */
    public function getMineDetail(Request $request): Response
    {
        $id = $request->input('id');
        
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        
        $data = $this->logic->getMineDetail($id);
        
        if (!$data) {
            return $this->fail('挖矿项目不存在');
        }
        
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

    /**
     * 获取挖矿统计
     * @param Request $request
     * @return Response
     */
    public function getMineStats(Request $request): Response
    {
        $type = $request->input('type');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $data = $this->logic->getMineStats($type, $startTime, $endTime);
        return $this->success($data);
    }
}