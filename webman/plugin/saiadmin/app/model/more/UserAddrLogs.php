<?php
declare (strict_types = 1);

namespace plugin\saiadmin\app\model\more;

use plugin\saiadmin\basic\BaseModel;

/**
 * @mixin \think\Model
 */
class UserAddrLogs extends BaseModel
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    protected $table = 'sa_user_addr_logs';
}
