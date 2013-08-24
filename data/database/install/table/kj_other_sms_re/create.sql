#-----------------创建表--- kj_other_sms_re
DROP TABLE IF EXISTS `{DB_PRE}other_sms_re`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_sms_re` (
`re_id` int(11) NOT NULL auto_increment,`re_cont` varchar(255) NOT NULL COMMENT '回复内容',`re_tel` varchar(20) NOT NULL COMMENT '回复电话',`re_addtime` int(11) NOT NULL COMMENT '回复保存时间',`re_time` datetime NOT NULL COMMENT '回复时间',`re_day` date NOT NULL COMMENT '回复日期',PRIMARY KEY (`re_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

