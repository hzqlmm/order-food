#-----------------创建表--- kj_sys_config
DROP TABLE IF EXISTS `{DB_PRE}sys_config`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_config` (
`config_id` int(10) NOT NULL auto_increment,`config_name` varchar(50) NOT NULL,`config_val` varchar(500) NOT NULL,`config_intro` varchar(100) NOT NULL,`config_readonly` tinyint(1) NOT NULL,`config_list` varchar(200) NOT NULL,`config_type` varchar(20) NOT NULL,`config_module` varchar(20) NOT NULL,`config_sort` int(10) default '0' COMMENT '排序',`config_env` varchar(50) NOT NULL COMMENT '环境',PRIMARY KEY (`config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=289

