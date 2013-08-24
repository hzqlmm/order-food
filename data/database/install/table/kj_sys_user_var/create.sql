#-----------------创建表--- kj_sys_user_var
DROP TABLE IF EXISTS `{DB_PRE}sys_user_var`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_var` (
`var_user_id` int(10) NOT NULL,`var_val` text NOT NULL,PRIMARY KEY (`var_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

