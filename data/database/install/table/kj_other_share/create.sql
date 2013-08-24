#-----------------创建表--- kj_other_share
DROP TABLE IF EXISTS `{DB_PRE}other_share`;
CREATE TABLE IF NOT EXISTS `{DB_PRE}other_share` (
`share_id` int(10) NOT NULL auto_increment,`share_url` varchar(100) NOT NULL COMMENT '享分地址',`share_user_id` int(10) NOT NULL COMMENT '用户',`share_date` date NOT NULL COMMENT '日期',`share_datetime` datetime NOT NULL COMMENT '时间',`share_addtime` int(10) NOT NULL,`share_type` smallint(2) NOT NULL COMMENT '类型',`share_target` smallint(2) NOT NULL COMMENT '分享到平台',`share_num` int(10) NOT NULL COMMENT '返回数',PRIMARY KEY (`share_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1

