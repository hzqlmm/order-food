#-----------------创建表--- kj_sys_user_group
DROP TABLE IF EXISTS `{DB_PRE}sys_user_group`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_group` (
`group_id` int(10) NOT NULL auto_increment,`group_name` varchar(50) NOT NULL COMMENT '分组名称',`group_addtime` int(10) NOT NULL COMMENT '添加时间',`group_updatetime` int(10) NOT NULL COMMENT '修改时间',`group_sort` int(5) NOT NULL COMMENT '排序',`group_pid` int(10) NOT NULL COMMENT '父级id',`group_pids` varchar(100) NOT NULL COMMENT '所有父级id',`group_limit` text NOT NULL COMMENT '权限值,序列化了的数组',`group_limit_article` text NOT NULL COMMENT '文章权限值,序列化了的数组',PRIMARY KEY (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

