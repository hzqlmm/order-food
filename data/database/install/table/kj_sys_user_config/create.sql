#-----------------创建表--- kj_sys_user_config
DROP TABLE IF EXISTS `{DB_PRE}sys_user_config`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_config` (
`config_user_id` int(11) NOT NULL,`config_fields` text NOT NULL COMMENT '用户自定义管理显示字段',`config_info` text NOT NULL COMMENT '用户自定义排序',PRIMARY KEY (`config_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

