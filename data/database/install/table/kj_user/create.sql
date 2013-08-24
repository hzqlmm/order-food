#-----------------创建表--- kj_user
DROP TABLE IF EXISTS `{DB_PRE}user`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}user` (
`user_id` int(10) NOT NULL auto_increment,`user_name` varchar(50) NOT NULL COMMENT '用户名',`user_pwd` varchar(50) NOT NULL COMMENT '密码',`user_pwd_key` varchar(10) NOT NULL COMMENT '密码加密值',`user_addtime` int(10) default '0',PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

