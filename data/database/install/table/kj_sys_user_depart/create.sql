#-----------------创建表--- kj_sys_user_depart
DROP TABLE IF EXISTS `{DB_PRE}sys_user_depart`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}sys_user_depart` (
`depart_id` int(10) NOT NULL auto_increment,`depart_name` varchar(50) NOT NULL COMMENT '分组名称',`depart_addtime` int(10) NOT NULL COMMENT '添加时间',`depart_updatetime` int(10) NOT NULL COMMENT '修改时间',`depart_sort` int(5) NOT NULL COMMENT '排序',`depart_pid` int(10) NOT NULL COMMENT '父级id',`depart_pids` varchar(100) NOT NULL COMMENT '所有父级id',`depart_depth` smallint(2) NOT NULL,PRIMARY KEY (`depart_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

