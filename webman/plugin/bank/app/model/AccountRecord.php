<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 用户资产记录表模型
 */
class AccountRecord extends BaseModel
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
    protected $table = 'account_record';

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 用户ID 搜索
     */
    public function searchUserIdAttr($query, $value)
    {
        $query->where('user_id', $value);
    }

    /**
     * 变化类型 搜索
     */
    public function searchChangeTypeAttr($query, $value)
    {
        $query->where('change_type', $value);
    }

    /**
     * 拖管天数 搜索
     */
    public function searchDaysAttr($query, $value)
    {
        $query->where('days', $value);
    }
}