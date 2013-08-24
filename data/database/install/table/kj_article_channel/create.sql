#-----------------创建表--- kj_article_channel
DROP TABLE IF EXISTS `{DB_PRE}article_channel`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}article_channel` (
`channel_id` int(10) NOT NULL auto_increment,`channel_name` varchar(50) NOT NULL COMMENT '频道名称',`channel_html` tinyint(1) NOT NULL COMMENT '是否生成静态文件',`channel_html_dir` varchar(50) NOT NULL COMMENT '静态文件生成目录',`channel_html_dirstyle` smallint(2) NOT NULL COMMENT '静态文件目录格式',`channel_addtime` int(10) NOT NULL COMMENT '添加时间',`channel_updatetime` int(10) NOT NULL COMMENT '修改时间',`channel_state` smallint(2) NOT NULL COMMENT '状态',`channel_article_tpl` varchar(100) NOT NULL COMMENT '文章模板',`channel_folder_tpl` varchar(100) NOT NULL COMMENT '目录模板',`channel_tpl` varchar(100) NOT NULL COMMENT '频道模板',`channel_html_ext` varchar(10) NOT NULL COMMENT '静态文件扩展名',`channel_mode` tinyint(1) NOT NULL COMMENT '频道模式：目前文章，图片',`channel_key` varchar(50) NOT NULL COMMENT '唯一关键词',PRIMARY KEY (`channel_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13

