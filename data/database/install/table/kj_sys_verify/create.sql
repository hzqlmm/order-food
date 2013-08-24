#-----------------创建表--- kj_sys_verify
DROP TABLE IF EXISTS `{DB_PRE}sys_verify`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_verify` (
`verify_id` int(10) NOT NULL auto_increment,`verify_user_id` int(10) NOT NULL COMMENT '用户id',`verify_type` smallint(2) NOT NULL COMMENT '验证类型',`verify_key` varchar(100) NOT NULL COMMENT '验证字符',`verify_retime` datetime NOT NULL,`verify_time` datetime NOT NULL,`verify_state` smallint(2) NOT NULL,PRIMARY KEY (`verify_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

