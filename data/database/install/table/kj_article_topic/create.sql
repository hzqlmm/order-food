#-----------------创建表--- kj_article_topic
DROP TABLE IF EXISTS `{DB_PRE}article_topic`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}article_topic` (
`topic_id` int(10) NOT NULL auto_increment,`topic_name` varchar(50) NOT NULL,`topic_tpl` varchar(100) NOT NULL,`topic_addtime` int(10) NOT NULL,`topic_state` smallint(2) NOT NULL,`topic_updatetime` int(10) NOT NULL,`topic_sort` int(10) NOT NULL,`topic_pic` varchar(100) NOT NULL,PRIMARY KEY (`topic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7

