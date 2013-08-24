#-----------------创建表--- kj_sys_session
DROP TABLE IF EXISTS `{DB_PRE}sys_session`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_session` (
`session_id` varchar(30) NOT NULL,`session_user_id` int(10) NOT NULL,`session_group_id` int(10) NOT NULL,`session_val` text NOT NULL,`session_addtime` int(10) NOT NULL,`session_updatetime` int(10) NOT NULL,PRIMARY KEY (`session_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

