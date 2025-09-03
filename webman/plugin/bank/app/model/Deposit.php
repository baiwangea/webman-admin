<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 用户拖管表模型
 */
class Deposit extends BaseModel
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
    protected $table = 'deposit';

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
     * 拖管类型 搜索
     */
    public function searchDepositTypeAttr($query, $value)
    {
        $query->where('deposit_type', $value);
    }

    /**
     * 拖管状态 搜索
     */
    public function searchDepositStatusAttr($query, $value)
    {
        $query->where('deposit_status', $value);
    }

    /**
     * 拖管天数 搜索
     */
    public function searchDaysAttr($query, $value)
    {
        $query->where('days', $value);
    }
}