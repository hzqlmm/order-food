#-----------------创建表--- kj_article_folder
DROP TABLE IF EXISTS `{DB_PRE}article_folder`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}article_folder` (
`folder_id` int(10) NOT NULL auto_increment,`folder_name` varchar(50) NOT NULL,`folder_addtime` int(10) NOT NULL,`folder_updatetime` int(10) NOT NULL,`folder_pids` varchar(200) NOT NULL,`folder_pid` int(10) NOT NULL,`folder_channel_id` int(10) NOT NULL,`folder_sort` int(10) NOT NULL,`folder_tpl` varchar(100) NOT NULL,`folder_article_tpl` varchar(100) NOT NULL,`folder_uid` int(10) NOT NULL,`folder_isdel` tinyint(1) NOT NULL,`folder_isdel_from` tinyint(1) NOT NULL COMMENT '从哪儿删除的',`folder_pic` varchar(100) NOT NULL,`folder_url` varchar(100) NOT NULL,PRIMARY KEY (`folder_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=57

