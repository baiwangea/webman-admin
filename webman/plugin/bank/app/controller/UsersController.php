<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\bank\basic\BaseController;
use plugin\bank\app\logic\UsersLogic;
use plugin\bank\app\validate\UsersValidate;
use support\Request;
use support\Response;

/**
 * 用户管理控制器
 */
class UsersController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new UsersLogic();
        $this->validate = new UsersValidate();
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
            ['user_name', ''],
            ['user_addr', ''],
            ['parent_addr', ''],
            ['status', ''],
            ['is_partner', ''],
            ['is_super_partner', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 读取
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function read(Request $request, $id = ''): Response
    {
        $id = $request->input('id', $id);
        $model = $this->logic->findOrEmpty($id);
        if ($model->isEmpty()) {
            return $this->fail('未查找到信息');
        }
        return $this->success($model->toArray());
    }

    /**
     * 获取用户邀请树
     * @param Request $request
     * @return Response
     */
    public function getInviteTree(Request $request): Response
    {
        $userAddr = $request->input('user_addr', '');
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $tree = $this->logic->getInviteTree($userAddr);
        return $this->success($tree);
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

    /**
     * 获取用户统计信息
     * @param Request $request
     * @return Response
     */
    public function getUserStats(Request $request): Response
    {
        $userAddr = $request->input('user_addr', '');
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $stats = $this->logic->getUserStats($userAddr);
        return $this->success($stats);
    }

    /**
     * 更新用户登录信息
     * @param Request $request
     * @return Response
     */
    public function updateLoginInfo(Request $request): Response
    {
        $userAddr = $request->input('user_addr', '');
        $loginIp = $request->input('login_ip', '');
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $result = $this->logic->updateLoginInfo($userAddr, $loginIp);
        if ($result) {
            return $this->success('更新成功');
        } else {
            return $this->fail('更新失败');
        }
    }


}
