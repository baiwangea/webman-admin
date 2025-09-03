<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------
namespace plugin\bank\basic;

use think\Validate;

/**
 * 验证器基础类
 * @package plugin\bank\basic
 */
class BaseValidate extends Validate
{
    /**
     * 验证是否唯一
     * @access public
     * @param mixed  $value 字段值
     * @param mixed  $rule  验证规则 格式：数据表,字段名,排除ID,主键名
     * @param array  $data  数据
     * @param string $field 验证字段名
     * @return bool
     */
    public function unique($value, $rule, array $data = [], string $field = ''): bool
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }

        if (str_contains($rule[0], '\\')) {
            // 指定模型类
            $db = new $rule[0];
        } else {
            return false;
        }

        $key = $rule[1] ?? $field;
        $map = [];

        if (str_contains($key, '^')) {
            // 支持多个字段验证
            $fields = explode('^', $key);
            foreach ($fields as $key) {
                if (isset($data[$key])) {
                    $map[] = [$key, '=', $data[$key]];
                }
            }
        } elseif (strpos($key, '=')) {
            // 支持复杂验证
            parse_str($key, $array);
            foreach ($array as $k => $val) {
                $map[] = [$k, '=', $data[$k] ?? $val];
            }
        } elseif (isset($data[$field])) {
            $map[] = [$key, '=', $data[$field]];
        }

        $pk = !empty($rule[3]) ? $rule[3] : $db->getPk();

        if (is_string($pk)) {
            if (isset($rule[2])) {
                $map[] = [$pk, '<>', $rule[2]];
            } elseif (isset($data[$pk])) {
                $map[] = [$pk, '<>', $data[$pk]];
            }
        }

        if ($db->where($map)->field($pk)->find()) {
            return false;
        }

        return true;
    }

    /**
     * 验证地址格式
     * @param mixed $value
     * @param mixed $rule
     * @param array $data
     * @param string $field
     * @return bool
     */
    public function checkAddress($value, $rule, array $data = [], string $field = ''): bool
    {
        // 简单的地址格式验证
        return strlen($value) === 42 && str_starts_with($value, '0x');
    }

    /**
     * 验证金额格式
     * @param mixed $value
     * @param mixed $rule
     * @param array $data
     * @param string $field
     * @return bool
     */
    public function checkAmount($value, $rule, array $data = [], string $field = ''): bool
    {
        return is_numeric($value) && $value >= 0;
    }
}