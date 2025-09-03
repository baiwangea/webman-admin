<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 用户购买等级表模型
 */
class DepositLevel extends BaseModel
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
    protected $table = 'deposit_level';

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
     * 上链状态 搜索
     */
    public function searchChainStatusAttr($query, $value)
    {
        $query->where('chain_status', $value);
    }

    /**
     * 合约地址 搜索
     */
    public function searchContractAddrAttr($query, $value)
    {
        $query->where('contract_addr', 'like', '%'.$value.'%');
    }
}