-- 为sa_system_user表添加谷歌验证码相关字段
ALTER TABLE `sa_system_user` 
ADD COLUMN `google_secret` varchar(32) NULL DEFAULT NULL COMMENT '谷歌验证器密钥' AFTER `backend_setting`,
ADD COLUMN `google_auth_enabled` tinyint(1) NULL DEFAULT 0 COMMENT '是否启用谷歌验证码 (0关闭 1开启)' AFTER `google_secret`;

-- 添加索引
ALTER TABLE `sa_system_user` ADD INDEX `idx_google_auth_enabled` (`google_auth_enabled`);