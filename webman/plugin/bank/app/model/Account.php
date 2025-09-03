<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 用户资产表模型
 */
class Account extends BaseModel
{
    protected $deleteTime = false;
    protected $updateTime = false;
    
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 数据库表名称
     * @var string
     */
    protected $table = 'account';

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 用户地址 搜索
     */
    public function searchUserAddrAttr($query, $value)
    {
        $query->where('user_addr', 'like', '%'.$value.'%');
    }

    /**
     * 用户ID 搜索
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id', $value);
    }

    /**
     * 钱包状态 搜索
     */
    public function searchWalletStatusAttr($query, $value)
    {
        $query->where('wallet_status', $value);
    }
}