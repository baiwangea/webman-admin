<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\Announcement;

/**
 * 公告逻辑层
 */
class AnnouncementLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new Announcement();
    }

    /**
     * 搜索处理器
     * @param array $where
     * @return \think\db\Query
     */
    public function search(array $where = []): \think\db\Query
    {
        $query = $this->model->withSearch(['title', 'status'], $where);
        return $query;
    }

    /**
     * 获取公告列表
     * @param array $where
     * @return array
     */
    public function getAnnouncementList(array $where = []): array
    {
        $query = $this->search($where);
        return $this->getList($query);
    }

    /**
     * 获取公告详情
     * @param int $id
     * @return array|null
     */
    public function getAnnouncementDetail(int $id): ?array
    {
        $announcement = $this->model->find($id);
        if (!$announcement) {
            return null;
        }
        
        return $announcement->toArray();
    }

    /**
     * 创建公告
     * @param array $data
     * @return int
     */
    public function createAnnouncement(array $data): int
    {
        $data['create_time'] = time();
        return $this->add($data);
    }

    /**
     * 更新公告
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateAnnouncement(int $id, array $data): bool
    {
        $data['update_time'] = time();
        return $this->edit($data, $id);
    }

    /**
     * 更新公告状态
     * @param int $id
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $id, int $status): bool
    {
        return $this->edit(['status' => $status, 'update_time' => time()], $id);
    }

    /**
     * 获取有效公告列表
     * @param int $limit
     * @return array
     */
    public function getActiveAnnouncements(int $limit = 10): array
    {
        return $this->model
            ->where('status', 1)
            ->order('create_time', 'desc')
            ->limit($limit)
            ->select()
            ->toArray();
    }
}