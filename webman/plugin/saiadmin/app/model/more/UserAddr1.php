<?php
declare (strict_types = 1);

namespace plugin\saiadmin\app\model\more;

use plugin\saiadmin\basic\BaseModel;

/**
 * @mixin \think\Model
 */
class UserAddr1 extends BaseModel
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    protected $table = 'sa_user_addr1';
    /**
     * 伞下业绩
     */
    public function yeji()
    {
        return $this->hasOne('plugin\bank\app\model\UserWaterBills', 'user_addr', 'user_addr');
    }
}
