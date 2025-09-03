<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\bank\app\logic;

use plugin\bank\basic\BaseLogic;
use plugin\bank\app\model\Users;
use plugin\saiadmin\exception\ApiException;

/**
 * 用户逻辑层
 */
class UsersLogic extends BaseLogic
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->model = new Users();
    }

    /**
     * 获取用户详情
     * @param int $id
     * @return array|null
     */
    public function getUserDetail(int $id): ?array
    {
        $user = $this->model->findOrEmpty($id);
        if ($user->isEmpty()) {
            return null;
        }
        
        $data = $user->toArray();
        // 获取邀请人数
        $data['invite_count'] = $this->model->where('parent_addr', $user->user_addr)->count();
        // 获取团队总人数
        $data['team_count'] = $this->getTeamCount($user->user_addr);
        
        return $data;
    }

    /**
     * 获取用户邀请树
     * @param string $userAddr
     * @param int $level
     * @return array
     */
    public function getInviteTree(string $userAddr, int $level = 3): array
    {
        $tree = [];
        $this->buildInviteTree($userAddr, $tree, 1, $level);
        return $tree;
    }

    /**
     * 构建邀请树
     * @param string $userAddr
     * @param array $tree
     * @param int $currentLevel
     * @param int $maxLevel
     */
    private function buildInviteTree(string $userAddr, array &$tree, int $currentLevel, int $maxLevel): void
    {
        if ($currentLevel > $maxLevel) {
            return;
        }
        
        $children = $this->model->where('parent_addr', $userAddr)
            ->field('id,user_name,user_addr,create_time')
            ->select()
            ->toArray();
        
        foreach ($children as $child) {
            $childData = [
                'id' => $child['id'],
                'user_name' => $child['user_name'],
                'user_addr' => $child['user_addr'],
                'create_time' => $child['create_time'],
                'level' => $currentLevel,
                'children' => []
            ];
            
            $this->buildInviteTree($child['user_addr'], $childData['children'], $currentLevel + 1, $maxLevel);
            $tree[] = $childData;
        }
    }

    /**
     * 设置合伙人状态
     * @param int $id
     * @param string $type
     * @param int $status
     * @return bool
     */
    public function setPartnerStatus(int $id, string $type, int $status): bool
    {
        $user = $this->model->findOrEmpty($id);
        if ($user->isEmpty()) {
            throw new ApiException('用户不存在');
        }
        
        $updateData = ['update_time' => time()];
        
        switch ($type) {
            case 'partner':
                $updateData['is_partner'] = $status;
                break;
            case 'super_partner':
                $updateData['is_super_partner'] = $status;
                break;
            case 'zline':
                $updateData['is_zline'] = $status;
                break;
            case 'community':
                $updateData['is_community'] = $status;
                break;
            default:
                throw new ApiException('无效的合伙人类型');
        }
        
        return $user->save($updateData);
    }

    /**
     * 获取用户统计信息
     * @param string $userAddr
     * @return array
     */
    public function getUserStats(string $userAddr): array
    {
        $user = $this->model->where('user_addr', $userAddr)->find();
        if (!$user) {
            throw new ApiException('用户不存在');
        }
        
        return [
            'direct_invite_count' => $this->model->where('parent_addr', $userAddr)->count(),
            'team_count' => $this->getTeamCount($userAddr),
            'partner_count' => $this->getPartnerCount($userAddr),
            'super_partner_count' => $this->getSuperPartnerCount($userAddr),
        ];
    }

    /**
     * 获取团队总人数
     * @param string $userAddr
     * @return int
     */
    private function getTeamCount(string $userAddr): int
    {
        $user = $this->model->where('user_addr', $userAddr)->find();
        if (!$user || empty($user->tree)) {
            return 0;
        }
        
        // 根据tree字段统计团队人数
        return $this->model->whereRaw("FIND_IN_SET('{$userAddr}', tree) > 0")
            ->where('user_addr', '<>', $userAddr)
            ->count();
    }

    /**
     * 获取合伙人数量
     * @param string $userAddr
     * @return int
     */
    private function getPartnerCount(string $userAddr): int
    {
        $user = $this->model->where('user_addr', $userAddr)->find();
        if (!$user || empty($user->tree)) {
            return 0;
        }
        
        return $this->model->whereRaw("FIND_IN_SET('{$userAddr}', tree) > 0")
            ->where('user_addr', '<>', $userAddr)
            ->where('is_partner', 1)
            ->count();
    }

    /**
     * 获取超级合伙人数量
     * @param string $userAddr
     * @return int
     */
    private function getSuperPartnerCount(string $userAddr): int
    {
        $user = $this->model->where('user_addr', $userAddr)->find();
        if (!$user || empty($user->tree)) {
            return 0;
        }
        
        return $this->model->whereRaw("FIND_IN_SET('{$userAddr}', tree) > 0")
            ->where('user_addr', '<>', $userAddr)
            ->where('is_super_partner', 1)
            ->count();
    }

    /**
     * 更新用户登录信息
     * @param string $userAddr
     * @param string $loginIp
     * @return bool
     */
    public function updateLoginInfo(string $userAddr, string $loginIp): bool
    {
        $user = $this->model->where('user_addr', $userAddr)->find();
        if (!$user) {
            throw new ApiException('用户不存在');
        }
        
        return $user->save([
            'login_ip' => $loginIp,
            'last_login' => time(),
            'update_time' => time()
        ]);
    }

    /**
     * 创建用户
     * @param array $data
     * @return mixed
     */
    public function createUser(array $data): mixed
    {
        // 验证地址格式
        if (!$this->validateAddress($data['user_addr'])) {
            throw new ApiException('用户地址格式不正确');
        }
        
        // 检查用户是否已存在
        $exists = $this->model->where('user_addr', $data['user_addr'])->find();
        if ($exists) {
            throw new ApiException('该地址用户已存在');
        }
        
        // 生成邀请码
        $data['invite_code'] = $this->generateInviteCode();
        $data['status'] = $data['status'] ?? 1;
        $data['create_time'] = time();
        $data['update_time'] = time();
        
        // 处理上级关系
        if (!empty($data['parent_addr'])) {
            $parent = $this->model->where('user_addr', $data['parent_addr'])->find();
            if ($parent) {
                $data['parent'] = $parent->id;
                $data['tree'] = $parent->tree ? $parent->tree . ',' . $data['user_addr'] : $data['user_addr'];
            }
        } else {
            $data['tree'] = $data['user_addr'];
        }
        
        return $this->add($data);
    }

    /**
     * 生成邀请码
     * @return string
     */
    private function generateInviteCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            $exists = $this->model->where('invite_code', $code)->find();
        } while ($exists);
        
        return $code;
    }

}
