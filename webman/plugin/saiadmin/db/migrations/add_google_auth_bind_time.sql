-- 为sa_system_user表添加谷歌验证器绑定时间字段
ALTER TABLE `sa_system_user` 
ADD COLUMN `google_auth_bind_time` datetime NULL DEFAULT NULL COMMENT '谷歌验证器绑定时间' AFTER `google_auth_enabled`;