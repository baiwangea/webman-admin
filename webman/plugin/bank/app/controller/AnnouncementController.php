<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\AnnouncementLogic;
use plugin\bank\app\validate\AnnouncementValidate;
use support\Request;
use support\Response;

/**
 * 公告控制器
 */
class AnnouncementController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new AnnouncementLogic();
        $this->validate = new AnnouncementValidate();
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
            ['title', ''],
            ['status', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
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
            return $this->success('操作成功');
        } else {
            return $this->fail('操作失败');
        }
    }
}