<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saiadmin\app\logic;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use plugin\saiadmin\app\model\system\SystemUser;
use plugin\saiadmin\exception\ApiException;

/**
 * 谷歌验证器逻辑类
 */
class GoogleAuthLogic
{
    /**
     * Google2FA实例
     * @var Google2FA
     */
    protected $google2fa;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * 生成谷歌验证码密钥
     * @return string
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * 生成二维码URL
     * @param string $username 用户名
     * @param string $secret 密钥
     * @param string $company 公司名称
     * @return string
     */
    public function getQRCodeUrl(string $username, string $secret, string $company = '后台管理'): string
    {
        try {
            // 获取otpauth URL
            $otpauthUrl = $this->google2fa->getQRCodeUrl(
                $company,
                $username,
                $secret
            );
            
            // 创建二维码渲染器
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            
            // 生成二维码SVG
            $qrCodeSvg = $writer->writeString($otpauthUrl);
            
            // 返回base64编码的SVG数据URI
            return 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
        } catch (\Exception $e) {
            // 如果生成二维码失败，返回原始的otpauth URL
            return $this->google2fa->getQRCodeUrl(
                $company,
                $username,
                $secret
            );
        }
    }

    /**
     * 验证谷歌验证码
     * @param string $secret 密钥
     * @param string $code 验证码
     * @return bool
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * 为用户绑定谷歌验证器
     * @param int $userId 用户ID
     * @param string $secret 密钥
     * @param string $code 验证码
     * @return array
     * @throws ApiException
     */
    public function bindGoogleAuth(int $userId, string $secret, string $code): array
    {
        $user = SystemUser::find($userId);
        if (!$user) {
            throw new ApiException('用户不存在');
        }

        // 如果用户已经绑定了谷歌验证器
        if ($user->google_auth_enabled) {
            throw new ApiException('您已经绑定了谷歌验证器');
        }

        // 验证密钥格式
        if (empty($secret)) {
            throw new ApiException('密钥不能为空');
        }
        
        // 验证用户输入的验证码
        if (!$this->verifyCode($secret, $code)) {
            throw new ApiException('验证码错误，请重新输入');
        }

        // 保存密钥并启用谷歌验证
        $user->google_secret = $secret;
        $user->google_auth_enabled = true;
        $user->google_auth_bind_time = date('Y-m-d H:i:s');
        $user->save();

        return [
            'secret' => $secret,
            'qr_code_url' => $this->getQRCodeUrl($user->username, $secret)
        ];
    }

    /**
     * 解绑谷歌验证器
     * @param int $userId 用户ID
     * @param string $code 验证码
     * @return bool
     * @throws ApiException
     */
    public function unbindGoogleAuth(int $userId, string $code): bool
    {
        $user = SystemUser::find($userId);
        if (!$user) {
            throw new ApiException('用户不存在');
        }

        if (!$user->google_auth_enabled) {
            throw new ApiException('您尚未绑定谷歌验证器');
        }

        // 验证当前验证码
        if (!$this->verifyCode($user->google_secret, $code)) {
            throw new ApiException('验证码错误，请重新输入');
        }

        // 清除密钥并禁用谷歌验证
        $user->google_secret = null;
        $user->google_auth_enabled = false;
        $user->google_auth_bind_time = null;
        $user->save();

        return true;
    }

    /**
     * 获取用户的谷歌验证器绑定信息
     * @param int $userId 用户ID
     * @return array
     */
    public function getUserGoogleAuthInfo(int $userId): array
    {
        $user = SystemUser::find($userId);
        if (!$user) {
            throw new ApiException('用户不存在');
        }

        $result = [
            'is_bound' => $user->google_auth_enabled,
            'enabled' => $user->google_auth_enabled,
            'bind_time' => $user->google_auth_bind_time,
            'secret' => null,
            'qr_code_url' => null
        ];

        // 如果未绑定，不在这里生成密钥，由前端单独调用generateSecret接口
        // 这样可以避免不必要的密钥生成和潜在的错误提示

        return $result;
    }

    /**
     * 验证用户登录时的谷歌验证码
     * @param int $userId 用户ID
     * @param string $code 验证码
     * @return bool
     * @throws ApiException
     */
    public function verifyLoginCode(int $userId, string $code): bool
    {
        $user = SystemUser::find($userId);
        if (!$user) {
            throw new ApiException('用户不存在');
        }

        // 如果用户未启用谷歌验证，直接返回true
        if (!$user->google_auth_enabled) {
            return true;
        }

        // 如果启用了谷歌验证但没有提供验证码
        if (empty($code)) {
            throw new ApiException('请输入谷歌验证码');
        }

        // 验证谷歌验证码
        if (!$this->verifyCode($user->google_secret, $code)) {
            throw new ApiException('谷歌验证码错误');
        }

        return true;
    }
}