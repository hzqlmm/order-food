#-----------------创建表--- kj_other_attatch
DROP TABLE IF EXISTS `{DB_PRE}other_attatch`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_attatch` (
`attatch_id` int(10) NOT NULL auto_increment,`attatch_type` varchar(10) NOT NULL COMMENT '类型(图片，文件，音乐 等)',`attatch_size` int(10) NOT NULL COMMENT '大小',`attatch_addtime` int(10) NOT NULL COMMENT '添加时间',`attatch_path` varchar(100) NOT NULL COMMENT '路径',`attatch_ext` varchar(10) NOT NULL COMMENT '扩展名',`attatch_user_id` int(10) NOT NULL COMMENT '用户id',`attatch_filename` varchar(50) NOT NULL COMMENT '文件名',`attatch_ip` varchar(20) NOT NULL,`attatch_small` varchar(100) NOT NULL,`attatch_small_name` varchar(50) NOT NULL,PRIMARY KEY (`attatch_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

