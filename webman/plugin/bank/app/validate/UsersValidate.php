<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 用户验证器
 */
class UsersValidate extends BaseValidate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'user_name' => 'require|length:2,50',
        'user_addr' => 'require|length:42,42|checkAddress',
        'parent_addr' => 'length:42,42|checkAddress',
        'invite_code' => 'length:6,20|unique:users,invite_code',
        'status' => 'in:0,1',
        'is_partner' => 'in:0,1',
        'is_super_partner' => 'in:0,1',
        'is_zline' => 'in:0,1',
        'is_community' => 'in:0,1',
        'login_ip' => 'ip',
        'last_login' => 'integer',
        'tree' => 'max:1000',
        'parent' => 'integer'
    ];

    /**
     * 错误信息
     * @var array
     */
    protected $message = [
        'user_name.require' => '用户名不能为空',
        'user_name.length' => '用户名长度必须在2-50个字符之间',
        'user_addr.require' => '用户地址不能为空',
        'user_addr.length' => '用户地址长度必须为42个字符',
        'user_addr.checkAddress' => '用户地址格式不正确',
        'parent_addr.length' => '上级地址长度必须为42个字符',
        'parent_addr.checkAddress' => '上级地址格式不正确',
        'invite_code.length' => '邀请码长度必须在6-20个字符之间',
        'invite_code.unique' => '邀请码已存在',
        'status.in' => '状态值必须为0或1',
        'is_partner.in' => '合伙人状态必须为0或1',
        'is_super_partner.in' => '超级合伙人状态必须为0或1',
        'is_zline.in' => 'Z线状态必须为0或1',
        'is_community.in' => '社区状态必须为0或1',
        'login_ip.ip' => '登录IP格式不正确',
        'last_login.integer' => '最后登录时间必须为整数',
        'tree.max' => '关系树长度不能超过1000个字符',
        'parent.integer' => '上级ID必须为整数'
    ];

    /**
     * 验证场景
     * @var array
     */
    protected $scene = [
        'save' => ['user_name', 'user_addr', 'parent_addr', 'invite_code', 'status'],
        'update' => ['user_name', 'parent_addr', 'invite_code', 'status'],
        'status' => ['status'],
        'partner' => ['is_partner', 'is_super_partner', 'is_zline', 'is_community'],
        'login' => ['login_ip', 'last_login']
    ];

    /**
     * 自定义验证地址格式
     * @param mixed $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool|string
     */
    public function checkAddress($value, $rule, array $data = [], string $field = ''): bool
    {
        if (empty($value)) {
            return true; // 空值由require规则处理
        }
        
        // 检查是否为有效的以太坊地址格式
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
            return '地址格式不正确，必须是有效的以太坊地址';
        }
        
        return true;
    }

    /**
     * 自定义验证唯一性
     * @param mixed $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool|string
     */
    public function unique($value, $rule, array $data = [], string $field = ''): bool
    {
        if (empty($value)) {
            return true;
        }
        
        list($table, $field) = explode(',', $rule);
        $model = new \plugin\bank\app\model\Users();
        
        $query = $model->where($field, $value);
        
        // 更新时排除当前记录
        if (isset($data['id']) && $data['id']) {
            $query->where('id', '<>', $data['id']);
        }
        
        $exists = $query->find();
        
        return $exists ? '该值已存在' : true;
    }
}
