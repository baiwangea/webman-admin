<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace plugin\bank\app\validate;

use plugin\bank\basic\BaseValidate;

/**
 * 公告验证器
 */
class AnnouncementValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名' => ['规则1','规则2'...]
     */
    protected $rule = [
        'title' => 'require|max:200',
        'content' => 'require',
        'type' => 'integer|between:1,3',
        'status' => 'integer|in:0,1',
        'sort' => 'integer|egt:0',
        'is_top' => 'integer|in:0,1',
        'start_time' => 'integer|egt:0',
        'end_time' => 'integer|egt:0',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则' => '错误信息'
     */
    protected $message = [
        'title.require' => '公告标题不能为空',
        'title.max' => '公告标题不能超过200个字符',
        'content.require' => '公告内容不能为空',
        'type.integer' => '公告类型必须是整数',
        'type.between' => '公告类型值必须在1-3之间',
        'status.integer' => '状态必须是整数',
        'status.in' => '状态值只能是0或1',
        'sort.integer' => '排序必须是整数',
        'sort.egt' => '排序值不能小于0',
        'is_top.integer' => '置顶标识必须是整数',
        'is_top.in' => '置顶标识只能是0或1',
        'start_time.integer' => '开始时间必须是整数',
        'start_time.egt' => '开始时间不能小于0',
        'end_time.integer' => '结束时间必须是整数',
        'end_time.egt' => '结束时间不能小于0',
    ];

    /**
     * 定义验证场景
     */
    protected $scene = [
        'save' => ['title', 'content', 'type', 'status', 'sort', 'is_top', 'start_time', 'end_time'],
        'update' => ['title', 'content', 'type', 'status', 'sort', 'is_top', 'start_time', 'end_time'],
        'status' => ['status'],
    ];

    /**
     * 验证时间范围
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkTimeRange($value, $rule, $data)
    {
        if (isset($data['start_time']) && isset($data['end_time'])) {
            if ($data['start_time'] > 0 && $data['end_time'] > 0 && $data['start_time'] >= $data['end_time']) {
                return '开始时间不能大于或等于结束时间';
            }
        }
        return true;
    }
}