<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 自定义数据表模型
 */
class CustomizeData extends BaseModel
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
    protected $table = 'customize_data';

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = 'mysql2';
}