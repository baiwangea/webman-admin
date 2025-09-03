<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saiadmin\app\controller;

use support\Request;
use support\Response;
use plugin\saiadmin\basic\BaseController;
use plugin\saiadmin\app\logic\GoogleAuthLogic;

/**
 * 谷歌验证码控制器
 */
class GoogleAuthController extends BaseController
{
    /**
     * 谷歌验证码服务/**
     * @var GoogleAuthLogic
     */
    protected $googleAuthLogic;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->googleAuthLogic = new GoogleAuthLogic();
        parent::__construct();
    }

    /**
     * 获取谷歌验证码绑定信息
     * @return Response
     */
    public function getBindInfo(): Response
    {
        try {
            $data = $this->googleAuthLogic->getUserGoogleAuthInfo($this->adminId);
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 绑定谷歌验证器
     * @param Request $request
     * @return Response
     */
    public function bind(Request $request): Response
    {
        $code = $request->post('code', '');
        $secret = $request->post('secret', '');
        
        if (empty($code)) {
            return $this->fail('请输入谷歌验证码');
        }
        
        if (empty($secret)) {
            return $this->fail('密钥不能为空');
        }

        try {
            // 绑定谷歌验证器
            $result = $this->googleAuthLogic->bindGoogleAuth($this->adminId, $secret, $code);
            return $this->success($result, '谷歌验证器绑定成功');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 解绑谷歌验证器
     * @param Request $request
     * @return Response
     */
    public function unbind(Request $request): Response
    {
        $code = $request->post('code', '');
        
        if (empty($code)) {
            return $this->fail('请输入谷歌验证码');
        }

        try {
            $result = $this->googleAuthLogic->unbindGoogleAuth($this->adminId, $code);
            if ($result) {
                return $this->success([], '谷歌验证器解绑成功');
            } else {
                return $this->fail('解绑失败');
            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 验证谷歌验证码
     * @param Request $request
     * @return Response
     */
    public function verify(Request $request): Response
    {
        $code = $request->post('code', '');
        
        if (empty($code)) {
            return $this->fail('请输入谷歌验证码');
        }

        try {
            $user = \plugin\saiadmin\app\model\system\SystemUser::find($this->adminId);
            if (!$user->google_auth_enabled) {
                return $this->fail('您尚未绑定谷歌验证器');
            }
            
            $result = $this->googleAuthLogic->verifyCode($user->google_secret, $code);
            if ($result) {
                return $this->success([], '验证成功');
            } else {
                return $this->fail('验证码错误');
            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 生成新的密钥和二维码
     * @return Response
     */
    public function generateSecret(): Response
    {
        try {
            $user = \plugin\saiadmin\app\model\system\SystemUser::find($this->adminId);
            
            // 检查用户是否已经绑定了谷歌验证器
            if ($user->google_auth_enabled) {
                return $this->fail('您已经绑定了谷歌验证器，如需重新绑定请先解绑');
            }
            
            $secret = $this->googleAuthLogic->generateSecretKey();
            $company = \plugin\saiadmin\app\model\system\SystemConfig::where('key', 'site_name')->value('value');
            $qrCodeUrl = $this->googleAuthLogic->getQRCodeUrl($user->username, $secret, $company);
            
            return $this->success([
                'secret' => $secret,
                'qr_code_url' => $qrCodeUrl
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}