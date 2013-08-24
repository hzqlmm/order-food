#-----------------创建表--- kj_other_msg
DROP TABLE IF EXISTS `{DB_PRE}other_msg`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_msg` (
`msg_id` int(10) NOT NULL auto_increment,`msg_name` varchar(20) NOT NULL,`msg_email` varchar(50) NOT NULL,`msg_tel` varchar(20) NOT NULL,`msg_type` smallint(2) NOT NULL,`msg_cont` text NOT NULL,`msg_time` datetime NOT NULL,`msg_recont` text NOT NULL,`msg_retime` datetime NOT NULL,`msg_isread` tinyint(4) NOT NULL,`msg_user_id` int(10) NOT NULL,`msg_ip` varchar(20) NOT NULL,PRIMARY KEY (`msg_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

