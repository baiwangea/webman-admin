<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 用户信息表模型
 */
class Users extends BaseModel
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
    protected $table = 'users';

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = 'mysql2';

    /**
     * 用户名 搜索
     */
    public function searchUserNameAttr($query, $value)
    {
        $query->where('user_name', 'like', '%'.$value.'%');
    }

}
