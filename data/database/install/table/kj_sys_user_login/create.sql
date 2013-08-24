#-----------------创建表--- kj_sys_user_login
DROP TABLE IF EXISTS `{DB_PRE}sys_user_login`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_login` (
`login_id` int(10) NOT NULL auto_increment,`login_user_id` int(10) NOT NULL COMMENT '用户id',`login_time` datetime NOT NULL COMMENT '登录时间',`login_day` date NOT NULL COMMENT '登录日期',`login_ip` varchar(20) NOT NULL COMMENT 'ip地址',`login_isreg` tinyint(1) NOT NULL,PRIMARY KEY (`login_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

