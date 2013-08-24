#-----------------创建表--- kj_other_link
DROP TABLE IF EXISTS `{DB_PRE}other_link`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_link` (
`link_id` int(11) NOT NULL auto_increment,`link_name` varchar(50) NOT NULL,`link_type` tinyint(1) default '0',`link_pic` varchar(100) NOT NULL,`link_url` varchar(100) NOT NULL,`link_sort` int(10) NOT NULL,`link_group` varchar(20) NOT NULL,PRIMARY KEY (`link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

