<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\controller;

use plugin\saiadmin\basic\BaseController;
use plugin\bank\app\logic\AccountRecordLogic;
use plugin\bank\app\validate\AccountRecordValidate;
use support\Request;
use support\Response;

/**
 * 账户记录控制器
 */
class AccountRecordController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new AccountRecordLogic();
        $this->validate = new AccountRecordValidate();
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
     * 获取用户账户记录
     */
    public function getUserRecords(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $type = $request->input('type', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 20);
        
        if (empty($userAddr)) {
            return $this->fail('用户地址不能为空');
        }
        
        $data = $this->logic->getUserRecords($userAddr, $type, $page, $limit);
        return $this->success('ok', $data);
    }

    /**
     * 获取账户记录统计
     */
    public function getRecordStats(Request $request): Response
    {
        $userAddr = $request->input('user_addr');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        
        $data = $this->logic->getRecordStats($userAddr, $startTime, $endTime);
        return $this->success('ok', $data);
    }
}